<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-white p-4 sm:p-6 lg:p-8 ">
        <div class="w-full max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-gray-400">
            <div class="hidden lg:flex flex-col justify-center items-center p-12 bg-gray-800/50 text-white text-center relative">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/30 to-gray-900/50"></div>
                <div class="relative z-10">

                    <h1 class="text-4xl font-extrabold tracking-tight">Kelola.web</h1>
                    <p class="mt-4 text-lg text-gray-300">Manajemen sumber daya manusia yang efisien dan modern.</p>
                </div>
            </div>
            <div class="p-8 sm:p-12 flex flex-col justify-center">
                <div class="w-full max-w-md mx-auto">
                    <div class="text-left mb-8">
                        <h2 class="text-3xl font-bold text-black">Login Akun</h2>
                        <p class="text-gray-400 mt-2">Selamat datang kembali! Silakan masukkan detail Anda.</p>
                    </div>
                    <x-auth-session-status class="mb-4" :status="session('status')" />
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="relative mb-6 group">
                            <x-text-input id="email" class="peer" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder=" " />
                            <x-input-label for="email" value="Alamat Email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div class="relative mt-4 group">
                            <x-text-input id="password" class="peer" type="password" name="password" required autocomplete="current-password" placeholder=" " />
                            <x-input-label for="password" value="Password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-between mt-6">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 bg-gray-400 text-indigo-500 shadow-sm focus:ring-gray-400 focus:ring-offset-gray-800" name="remember">
                                <span class="ml-2 text-sm text-gray-400">{{ __('Ingat saya') }}</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a class="underline text-sm text-indigo-400 hover:text-indigo-300 rounded-md" href="{{ route('password.request') }}">
                                    {{ __('Lupa password?') }}
                                </a>
                            @endif
                        </div>
                        <div class="mt-8">
                            <x-primary-button class="w-full justify-center text-base py-3">{{ __('Log In') }}</x-primary-button>
                        </div>
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-400">
                                Belum punya akun? <a href="{{ route('register') }}" class="font-medium text-indigo-400 hover:text-indigo-300">Daftar di sini</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
