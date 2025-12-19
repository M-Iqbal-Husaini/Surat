<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-md border border-gray-200 p-8">
            {{-- Logo --}}
            <div class="flex justify-center mb-4">
                <x-authentication-card-logo class="h-25 w-auto" />
            </div>

            {{-- Title --}}
            <h1 class="text-center text-[20px] font-bold text-gray-800">
                Selamat Datang Sistem E-Surat!
            </h1>
            <p class="text-center text-sm font-medium text-gray-500 mt-6 mb-5">
                Silahkan Masukkan Email dan Kata Sandi<br/>untuk Masuk ke Sistem
            </p>

            {{-- Errors / Status --}}
            <x-validation-errors class="mb-4" />

            @session('status')
                <div class="mb-4 text-sm font-medium text-green-600">
                    {{ $value }}
                </div>
            @endsession

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <x-input
                        id="email"
                        name="email"
                        type="email"
                        class="w-full"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Email"
                    />
                </div>

                {{-- Password with eye --}}
                <div x-data="{ show: false }" class="relative">
                    <x-input
                        id="password"
                        name="password"
                        class="w-full pr-11"
                        :type="`password`"
                        x-bind:type="show ? 'text' : 'password'"
                        required
                        autocomplete="current-password"
                        placeholder="Kata Sandi"
                    />

                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600"
                        @click="show = !show"
                        aria-label="Toggle password visibility"
                    >
                        {{-- simple eye icon (SVG) --}}
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>

                        <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M3 3l18 18M10.477 10.48a3 3 0 004.243 4.243M9.88 9.88A3 3 0 0114.12 14.12"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c1.6 0 3.115.374 4.45 1.042M21.542 12c-1.274 4.057-5.064 7-9.542 7-1.6 0-3.115-.374-4.45-1.042"/>
                        </svg>
                    </button>
                </div>

                {{-- Forgot --}}
                <div class="text-right">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-blue-600 hover:text-blue-700 underline"
                           href="{{ route('password.request') }}">
                            Lupa Kata Sandi?
                        </a>
                    @endif
                </div>

                {{-- Button --}}
                <button
                    type="submit"
                    class="w-full rounded-md bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 transition"
                >
                    Masuk
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
