<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WebAuthnCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WebAuthnController extends Controller
{
    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────

    private function b64uEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function b64uDecode(string $data): string
    {
        $pad = strlen($data) % 4;
        if ($pad) $data .= str_repeat('=', 4 - $pad);
        return base64_decode(strtr($data, '-_', '+/'));
    }

    private function generateChallenge(): string
    {
        return $this->b64uEncode(random_bytes(32));
    }

    /**
     * Ambil rpId (hostname saja, tanpa port).
     * WebAuthn tidak memperbolehkan port dalam rpId.
     * Bekerja di localhost:8000, 127.0.0.1:8000 (jika diakses via localhost), maupun domain HTTPS.
     */
    private function getRpId(Request $request): string
    {
        $host = $request->getHost(); // sudah tanpa port di Laravel
        // Jika masih ada port (jarang tapi jaga-jaga), strip:
        return parse_url('http://' . $host, PHP_URL_HOST) ?? $host;
    }

    /**
     * Bangun origin yang benar berdasarkan scheme + host + port (jika non-standar).
     * Origin berbeda dari rpId: origin BOLEH menyertakan port.
     */
    private function getOrigin(Request $request): string
    {
        $scheme = $request->getScheme();
        $host   = $request->getHost();
        $port   = $request->getPort();
        $isStdPort = ($scheme === 'http' && $port == 80) || ($scheme === 'https' && $port == 443);
        return $scheme . '://' . $host . ($isStdPort ? '' : ':' . $port);
    }

    /** Minimal CBOR decoder (handles WebAuthn use cases) */
    private function cborDecode(string $data, int &$offset = 0): mixed
    {
        if ($offset >= strlen($data)) throw new \Exception('CBOR: unexpected end');
        $initial   = ord($data[$offset++]);
        $majorType = ($initial >> 5) & 0x07;
        $addInfo   = $initial & 0x1f;

        $value = match (true) {
            $addInfo < 24  => $addInfo,
            $addInfo === 24 => ord($data[$offset++]),
            $addInfo === 25 => (function () use (&$data, &$offset) {
                $v = unpack('n', substr($data, $offset, 2))[1]; $offset += 2; return $v;
            })(),
            $addInfo === 26 => (function () use (&$data, &$offset) {
                $v = unpack('N', substr($data, $offset, 4))[1]; $offset += 4; return $v;
            })(),
            $addInfo === 27 => (function () use (&$data, &$offset) {
                $v = unpack('J', substr($data, $offset, 8))[1]; $offset += 8; return $v;
            })(),
            default => 0,
        };

        switch ($majorType) {
            case 0: return $value;
            case 1: return -1 - $value;
            case 2:
                $r = substr($data, $offset, $value); $offset += $value; return $r;
            case 3:
                $r = substr($data, $offset, $value); $offset += $value; return $r;
            case 4:
                $arr = [];
                for ($i = 0; $i < $value; $i++) $arr[] = $this->cborDecode($data, $offset);
                return $arr;
            case 5:
                $map = [];
                for ($i = 0; $i < $value; $i++) {
                    $k = $this->cborDecode($data, $offset);
                    $map[$k] = $this->cborDecode($data, $offset);
                }
                return $map;
            case 7:
                if ($addInfo === 20) return false;
                if ($addInfo === 21) return true;
                if ($addInfo === 22) return null;
                break;
        }
        throw new \Exception("Unsupported CBOR major type: {$majorType}");
    }

    /** Convert COSE EC P-256 key to PEM */
    private function coseKeyToPem(array $coseKey): string
    {
        $kty = $coseKey[1] ?? null;

        if ($kty === 2) { // EC key
            $x = $coseKey[-2] ?? null;
            $y = $coseKey[-3] ?? null;
            if (!$x || !$y || strlen($x) !== 32 || strlen($y) !== 32) {
                throw new \Exception('Invalid EC key coordinates');
            }
            // Fixed DER prefix for EC P-256 SubjectPublicKeyInfo
            $der = hex2bin('3059301306072a8648ce3d020106082a8648ce3d03010703420004') . $x . $y;
            return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END PUBLIC KEY-----\n";
        }

        if ($kty === 3) { // RSA key
            $n = $coseKey[-1] ?? null;
            $e = $coseKey[-2] ?? null;
            if (!$n || !$e) throw new \Exception('Invalid RSA key');

            // Encode as DER integers (add 0x00 prefix if high bit set)
            $encodeInt = fn($bytes) => (ord($bytes[0]) & 0x80) ? "\x00" . $bytes : $bytes;
            $nDer = $encodeInt($n);
            $eDer = $encodeInt($e);

            $buildTlv = function (string $tag, string $content): string {
                $len = strlen($content);
                if ($len < 128) return $tag . chr($len) . $content;
                if ($len < 256) return $tag . "\x81" . chr($len) . $content;
                return $tag . "\x82" . chr($len >> 8) . chr($len & 0xff) . $content;
            };

            $rsaSeq = $buildTlv("\x30",
                $buildTlv("\x02", $nDer) . $buildTlv("\x02", $eDer)
            );
            $spki = $buildTlv("\x30",
                hex2bin('300d06092a864886f70d0101010500') .
                $buildTlv("\x03", "\x00" . $rsaSeq)
            );
            return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($spki), 64, "\n") . "-----END PUBLIC KEY-----\n";
        }

        throw new \Exception("Unsupported COSE key type: {$kty}");
    }

    // ─────────────────────────────────────────
    // Registration
    // ─────────────────────────────────────────

    public function registerOptions(Request $request)
    {
        $user      = Auth::user();
        $challenge = $this->generateChallenge();
        $request->session()->put('webauthn_register_challenge', $challenge);

        // Exclude already-registered credentials
        $excludeCredentials = $user->webAuthnCredentials()->get()->map(fn($c) => [
            'type' => 'public-key',
            'id'   => $c->credential_id,
        ])->values();

        return response()->json([
            'rp'   => ['name' => config('app.name'), 'id' => $this->getRpId($request)],
            'user' => [
                'id'          => $this->b64uEncode(pack('N', $user->id)),
                'name'        => $user->username,
                'displayName' => $user->nama,
            ],
            'challenge'              => $challenge,
            'pubKeyCredParams'       => [
                ['type' => 'public-key', 'alg' => -7],
                ['type' => 'public-key', 'alg' => -257],
            ],
            'timeout'                => 60000,
            'authenticatorSelection' => [
                'authenticatorAttachment' => 'platform',
                'userVerification'        => 'required',
                'residentKey'             => 'required',
            ],
            'attestation'            => 'none',
            'excludeCredentials'     => $excludeCredentials,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate(['device_name' => 'required|string|max:100']);

        $challenge = $request->session()->get('webauthn_register_challenge');
        if (!$challenge) return response()->json(['error' => 'Challenge tidak ditemukan.'], 400);

        try {
            $user     = Auth::user();
            $response = $request->input('response');
            $origin   = $this->getOrigin($request);
            $rpIdHash = hash('sha256', $this->getRpId($request), true);

            // Parse clientDataJSON
            $clientData = json_decode($this->b64uDecode($response['clientDataJSON']), true);
            if ($clientData['type'] !== 'webauthn.create') throw new \Exception('Invalid type');
            if ($clientData['challenge'] !== $challenge) throw new \Exception('Challenge mismatch');
            if ($clientData['origin'] !== $origin) throw new \Exception('Origin mismatch');

            // Parse attestationObject (CBOR)
            $attestation = $this->cborDecode($this->b64uDecode($response['attestationObject']));
            $authData    = $attestation['authData'];

            // Parse authData
            if (strlen($authData) < 37) throw new \Exception('authData too short');
            if (substr($authData, 0, 32) !== $rpIdHash) throw new \Exception('rpIdHash mismatch');

            $flags  = ord($authData[32]);
            $up     = ($flags & 0x01) !== 0; // User Present
            $uv     = ($flags & 0x04) !== 0; // User Verified
            $at     = ($flags & 0x40) !== 0; // Attested Credential Data

            if (!$up || !$uv) throw new \Exception('User verification required');
            if (!$at) throw new \Exception('No attested credential data');

            $counter = unpack('N', substr($authData, 33, 4))[1];

            // Parse attested credential data
            $pos           = 37;
            $pos          += 16; // skip AAGUID
            $credIdLen     = unpack('n', substr($authData, $pos, 2))[1]; $pos += 2;
            $credentialId  = $this->b64uEncode(substr($authData, $pos, $credIdLen)); $pos += $credIdLen;
            $coseKeyBytes  = substr($authData, $pos);
            $offset        = 0;
            $coseKey       = $this->cborDecode($coseKeyBytes, $offset);
            $publicKeyPem  = $this->coseKeyToPem($coseKey);

            // Check duplicate
            if (WebAuthnCredential::where('credential_id', $credentialId)->exists()) {
                return response()->json(['error' => 'Credential sudah terdaftar.'], 409);
            }

            $user->webAuthnCredentials()->create([
                'credential_id' => $credentialId,
                'public_key'    => $publicKeyPem,
                'counter'       => $counter,
                'device_name'   => $request->device_name,
                'last_used_at'  => now(),
            ]);

            $request->session()->forget('webauthn_register_challenge');
            return response()->json(['message' => 'Perangkat berhasil didaftarkan!']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Registrasi gagal: ' . $e->getMessage()], 400);
        }
    }

    // ─────────────────────────────────────────
    // Authentication (guest)
    // ─────────────────────────────────────────

    public function loginOptions(Request $request)
    {
        // Username bersifat opsional:
        // - Jika diisi → filter allowCredentials untuk user tersebut
        // - Jika kosong → allowCredentials: [] → browser tampilkan account picker (passkey flow)
        $request->validate(['username' => 'nullable|string']);
        $user = $request->filled('username')
            ? User::where('username', $request->username)->first()
            : null;

        $challenge = $this->generateChallenge();
        $request->session()->put('webauthn_login_challenge', $challenge);
        $request->session()->put('webauthn_login_user_id', $user?->id);

        $allowCredentials = [];
        if ($user) {
            $allowCredentials = $user->webAuthnCredentials()->get()->map(fn($c) => [
                'type' => 'public-key',
                'id'   => $c->credential_id,
            ])->values();
        }
        // Jika $user null → allowCredentials tetap [] → browser tampilkan semua passkey yang tersimpan

        return response()->json([
            'challenge'        => $challenge,
            'timeout'          => 60000,
            'rpId'             => $this->getRpId($request),
            'allowCredentials' => $allowCredentials,
            'userVerification' => 'required',
        ]);
    }

    public function login(Request $request)
    {
        $challenge = $request->session()->get('webauthn_login_challenge');
        $userId    = $request->session()->get('webauthn_login_user_id');
        if (!$challenge) return response()->json(['error' => 'Challenge tidak ditemukan.'], 400);

        try {
            $response = $request->input('response');
            $origin   = $this->getOrigin($request);
            $rpIdHash = hash('sha256', $this->getRpId($request), true);

            // Find credential
            $credId     = $response['id'];
            $credential = WebAuthnCredential::where('credential_id', $credId)->first();
            if (!$credential) return response()->json(['error' => 'Credential tidak ditemukan.'], 404);

            // Parse clientDataJSON
            $clientData = json_decode($this->b64uDecode($response['clientDataJSON']), true);
            if ($clientData['type'] !== 'webauthn.get') throw new \Exception('Invalid type');
            if ($clientData['challenge'] !== $challenge) throw new \Exception('Challenge mismatch');
            if ($clientData['origin'] !== $origin) throw new \Exception('Origin mismatch');

            // Parse authenticatorData
            $authData = $this->b64uDecode($response['authenticatorData']);
            if (strlen($authData) < 37) throw new \Exception('authData too short');
            if (substr($authData, 0, 32) !== $rpIdHash) throw new \Exception('rpIdHash mismatch');

            $flags   = ord($authData[32]);
            $up      = ($flags & 0x01) !== 0;
            $uv      = ($flags & 0x04) !== 0;
            if (!$up || !$uv) throw new \Exception('User verification required');

            $counter = unpack('N', substr($authData, 33, 4))[1];
            if ($counter > 0 && $counter <= $credential->counter) {
                throw new \Exception('Counter replay detected');
            }

            // Verify signature
            $clientDataHash   = hash('sha256', $this->b64uDecode($response['clientDataJSON']), true);
            $signatureBase    = $authData . $clientDataHash;
            $signature        = $this->b64uDecode($response['signature']);
            $alg              = str_contains($credential->public_key, 'EC') ? OPENSSL_ALGO_SHA256 : OPENSSL_ALGO_SHA256;

            $verified = openssl_verify($signatureBase, $signature, $credential->public_key, $alg);
            if ($verified !== 1) throw new \Exception('Signature verification failed');

            // Update credential
            $credential->update(['counter' => $counter, 'last_used_at' => now()]);

            // Log in
            $user = $credential->user;
            Auth::login($user, true);

            $request->session()->forget(['webauthn_login_challenge', 'webauthn_login_user_id']);
            $request->session()->regenerate();

            // Determine redirect
            $redirect = match($user->role) {
                'pengurus'  => route('pengurus.dashboard'),
                'guru'      => route('guru.dashboard'),
                'orangtua'  => route('orangtua.dashboard'),
                default     => route('dashboard'),
            };

            return response()->json(['redirect' => $redirect]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Login gagal: ' . $e->getMessage()], 400);
        }
    }

    // ─────────────────────────────────────────
    // Delete credential
    // ─────────────────────────────────────────

    public function destroy(WebAuthnCredential $credential)
    {
        if ($credential->user_id !== Auth::id()) abort(403);
        $credential->delete();
        return back()->with('success', 'Perangkat berhasil dihapus.');
    }
}
