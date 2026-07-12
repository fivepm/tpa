<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gray-50 selection:bg-green-500 selection:text-white"
         x-data="loginPage()">

        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-lg rounded-xl border border-gray-100">
            <div class="mb-8 text-center">
                <a href="/" class="inline-flex justify-center mb-4">
                    <img src="{{ asset('logo.png') }}"
                         alt="Logo" class="h-20 w-20 rounded-full object-cover shadow-sm">
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Selamat Datang</h2>
                <p class="text-sm text-gray-500 mt-2">Silakan masuk ke akun Anda</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- Login Biasa --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-5">
                    <x-input-label for="username" :value="__('Username')" class="sr-only" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <x-text-input id="username" x-model="username"
                            class="block w-full pl-10 py-3 border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                            type="text" name="username" :value="old('username')"
                            placeholder="Username" required autofocus autocomplete="username" />
                    </div>
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <div class="mb-6">
                    <x-input-label for="password" :value="__('Password')" class="sr-only" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <x-text-input id="password"
                            class="block w-full pl-10 py-3 border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                            type="password" name="password" placeholder="Password"
                            required autocomplete="current-password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-green-600 hover:text-green-800" href="{{ route('password.request') }}">
                            {{ __('Lupa password?') }}
                        </a>
                    @endif
                </div>

                <x-primary-button class="w-full justify-center py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition duration-200 ease-in-out transform hover:-translate-y-0.5 shadow-md">
                    {{ __('Masuk') }}
                </x-primary-button>
            </form>

        </div>

        <div class="mt-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} e-presensi.
        </div>
    </div>

    <script>
    function loginPage() {
        return {
            username: '{{ old("username") }}',
            webAuthnSupported: false,
            webAuthnLoading: false,
            webAuthnError: '',

            async init() {
                try {
                    this.webAuthnSupported = !!(window.PublicKeyCredential &&
                        await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable());
                } catch { this.webAuthnSupported = false; }
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

            async fastLogin() {
                this.webAuthnLoading = true;
                this.webAuthnError   = '';
                try {
                    // 1. Get options — kirim username jika ada, kosong = browser tampilkan account picker
                    const optRes = await fetch('{{ route("webauthn.login.options") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ username: this.username.trim() || null }),
                    });
                    const opts = await optRes.json();
                    if (!optRes.ok) throw new Error(opts.error || 'Gagal mendapatkan opsi login');

                    opts.challenge = this.b64uDecode(opts.challenge);
                    if (opts.allowCredentials?.length) {
                        opts.allowCredentials = opts.allowCredentials.map(c => ({...c, id: this.b64uDecode(c.id)}));
                    }

                    // 2. Get assertion from authenticator
                    const assertion = await navigator.credentials.get({ publicKey: opts });

                    // 3. Send to server
                    const payload = {
                        response: {
                            id:                 this.b64uEncode(assertion.rawId),
                            clientDataJSON:     this.b64uEncode(assertion.response.clientDataJSON),
                            authenticatorData:  this.b64uEncode(assertion.response.authenticatorData),
                            signature:          this.b64uEncode(assertion.response.signature),
                        }
                    };
                    const loginRes = await fetch('{{ route("webauthn.login") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });
                    const result = await loginRes.json();
                    if (!loginRes.ok) throw new Error(result.error || 'Login gagal');
                    window.location.href = result.redirect;

                } catch (e) {
                    if (e.name === 'NotAllowedError') {
                        this.webAuthnError = 'Verifikasi dibatalkan atau tidak ada credential yang cocok.';
                    } else {
                        this.webAuthnError = e.message || 'Fast login gagal.';
                    }
                }
                this.webAuthnLoading = false;
            }
        }
    }
    </script>
</x-guest-layout>