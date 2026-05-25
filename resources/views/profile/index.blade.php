<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profil Saya</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash --}}
            @foreach (['success_password' => 'green', 'success' => 'green', 'error' => 'red'] as $key => $color)
                @if(session($key))
                    <div class="bg-{{ $color }}-100 border border-{{ $color }}-400 text-{{ $color }}-700 px-4 py-3 rounded mb-2">
                        {{ session($key) }}
                    </div>
                @endif
            @endforeach

            {{-- Info Akun --}}
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Akun</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Nama</dt>
                        <dd class="text-sm text-gray-900 font-semibold">{{ $user->nama }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Username</dt>
                        <dd class="text-sm text-gray-900">{{ $user->username }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Role</dt>
                        <dd>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $user->role === 'pengurus' ? 'bg-purple-100 text-purple-700' :
                                   ($user->role === 'guru' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Ganti Password --}}
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ganti Password</h3>
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-300 text-red-600 rounded p-3 mb-4 text-sm">
                        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                        <input type="password" name="current_password" required
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="password" required
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm py-2 px-3">
                    </div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700 transition">
                        Simpan Password
                    </button>
                </form>
            </div>

            {{-- Fast Login / Biometrik --}}
            <div class="bg-white shadow sm:rounded-lg p-6" x-data="webAuthnManager()">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Fast Login (Biometrik / PIN)</h3>
                        <p class="text-sm text-gray-500 mt-1">Daftarkan perangkat ini agar dapat login tanpa password menggunakan fingerprint, Face ID, atau PIN perangkat.</p>
                    </div>
                </div>

                {{-- Tidak didukung --}}
                <div x-show="!supported" class="text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded p-3 mb-4">
                    ⚠️ Browser atau perangkat ini tidak mendukung Fast Login.
                </div>

                {{-- Form daftarkan --}}
                <div x-show="supported" class="mb-6">
                    <div class="flex gap-3">
                        <input x-model="deviceName" type="text" placeholder="Nama perangkat (cth: HP Saya)"
                            class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm py-2 px-3">
                        <button @click="registerDevice()" :disabled="loading || !deviceName.trim()"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!loading">+ Daftarkan Perangkat Ini</span>
                            <span x-show="loading">Memproses...</span>
                        </button>
                    </div>
                    <p x-show="message" x-text="message"
                        :class="isError ? 'text-red-600 bg-red-50 border-red-200' : 'text-green-600 bg-green-50 border-green-200'"
                        class="mt-2 text-sm border rounded px-3 py-2"></p>
                </div>

                {{-- Daftar perangkat terdaftar --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Perangkat Terdaftar ({{ $credentials->count() }})</h4>
                    @forelse ($credentials as $cred)
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-lg">🔑</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $cred->device_name ?? 'Perangkat Tanpa Nama' }}</p>
                                    <p class="text-xs text-gray-500">
                                        Terdaftar: {{ $cred->created_at->translatedFormat('d F Y') }}
                                        @if($cred->last_used_at)
                                            · Terakhir digunakan: {{ $cred->last_used_at->translatedFormat('d F Y, H:i') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('webauthn.destroy', $cred->id) }}"
                                onsubmit="return confirm('Hapus perangkat ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 italic">Belum ada perangkat terdaftar.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <script>
    function webAuthnManager() {
        return {
            supported: false,
            loading: false,
            deviceName: '',
            message: '',
            isError: false,

            async init() {
                this.supported = !!(window.PublicKeyCredential &&
                    await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable().catch(() => false));
            },

            b64uDecode(str) {
                let s = str.replace(/-/g,'+').replace(/_/g,'/');
                while (s.length % 4) s += '=';
                return Uint8Array.from(atob(s), c => c.charCodeAt(0));
            },
            b64uEncode(buf) {
                return btoa(String.fromCharCode(...new Uint8Array(buf)))
                    .replace(/\+/g,'-').replace(/\//g,'_').replace(/=/g,'');
            },

            async registerDevice() {
                this.loading = true; this.message = ''; this.isError = false;
                try {
                    // 1. Get options
                    const optRes = await fetch('{{ route("webauthn.register.options") }}', {
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    const opts = await optRes.json();

                    // 2. Convert challenge & user.id from base64url to ArrayBuffer
                    opts.challenge = this.b64uDecode(opts.challenge);
                    opts.user.id  = this.b64uDecode(opts.user.id);
                    if (opts.excludeCredentials) {
                        opts.excludeCredentials = opts.excludeCredentials.map(c => ({...c, id: this.b64uDecode(c.id)}));
                    }

                    // 3. Create credential
                    const cred = await navigator.credentials.create({ publicKey: opts });

                    // 4. Send to server
                    const payload = {
                        device_name: this.deviceName,
                        response: {
                            clientDataJSON:    this.b64uEncode(cred.response.clientDataJSON),
                            attestationObject: this.b64uEncode(cred.response.attestationObject),
                        }
                    };
                    const reg = await fetch('{{ route("webauthn.register") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });
                    const result = await reg.json();
                    if (!reg.ok) throw new Error(result.error || 'Gagal mendaftarkan perangkat');
                    this.message = result.message;
                    this.isError = false;
                    setTimeout(() => location.reload(), 1500);
                } catch (e) {
                    this.message  = e.message || 'Pendaftaran dibatalkan atau gagal.';
                    this.isError  = true;
                }
                this.loading = false;
            }
        }
    }
    </script>
</x-app-layout>
