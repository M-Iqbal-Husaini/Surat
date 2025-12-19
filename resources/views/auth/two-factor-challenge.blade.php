<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            {{-- Header line like the example --}}

            <div class="p-8" x-data="{ recovery: false }">
                {{-- Title --}}
                <h1 class="text-center text-[25px] font-bold text-gray-800">
                    Two-Factor Authentication
                </h1>

                {{-- Subtitle --}}
                <p class="text-sm font-medium text-gray-500 mt-6 mb-2" x-show="!recovery">
                    Masukkan kode yang dihasilkan oleh aplikasi Authenticator
                </p>

                <p class="text-sm font-medium text-gray-500 mt-6 mb-2" x-cloak x-show="recovery">
                    Masukkan kode pemulihan untuk mengakses akun Anda.
                </p>

                <x-validation-errors class="mb-2" />

                <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-4">
                    @csrf

                    {{-- TOTP Code --}}
                    <div x-show="!recovery">
                        <x-input
                            id="code"
                            name="code"
                            type="text"
                            inputmode="numeric"
                            class="w-full"
                            autofocus
                            x-ref="code"
                            autocomplete="one-time-code"
                            placeholder="XXXXXX"
                        />
                    </div>

                    {{-- Recovery Code --}}
                    <div x-cloak x-show="recovery">
                        <x-input
                            id="recovery_code"
                            name="recovery_code"
                            type="text"
                            class="w-full"
                            x-ref="recovery_code"
                            autocomplete="one-time-code"
                            placeholder="XXXXXX"
                        />
                    </div>

                    {{-- Button full width --}}
                    <button
                        type="submit"
                        class="w-full rounded-md bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 transition"
                    >
                        Verifikasi
                    </button>

                    {{-- Link under button --}}
                    <div class="text-center pt-1">
                        <button
                            type="button"
                            class="text-sm text-blue-600 hover:text-blue-700 underline"
                            x-show="!recovery"
                            x-on:click="
                                recovery = true;
                                $nextTick(() => { $refs.recovery_code.focus() })
                            "
                        >
                            Gunakan Kode Pemulihan
                        </button>

                        <button
                            type="button"
                            class="text-sm text-blue-600 hover:text-blue-700 underline"
                            x-cloak
                            x-show="recovery"
                            x-on:click="
                                recovery = false;
                                $nextTick(() => { $refs.code.focus() })
                            "
                        >
                            Gunakan Kode Autentikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
