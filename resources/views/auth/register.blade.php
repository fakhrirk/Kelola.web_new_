<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-white p-4 sm:p-6 lg:p-8 ">
        <div class="w-full max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-gray-400">

            <!-- Kolom Kiri: Branding -->
            <div class="hidden lg:flex flex-col justify-center items-center p-12 bg-gray-800/50 text-white text-center relative">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/30 to-gray-900/50"></div>
                <div class="relative z-10">
                    <h1 class="text-4xl font-extrabold tracking-tight">Kelola.web</h1>
                    <p class="mt-4 text-lg text-gray-300">Manajemen sumber daya manusia yang efisien dan modern.</p>
                </div>
            </div>

            <!-- Kolom Kanan: Form Register -->
            <div class="p-8 sm:p-12 flex flex-col justify-center bg-white">
                <div class="w-full max-w-md mx-auto">
                    <div class="text-left mb-8">
                        <h2 class="text-3xl font-bold text-black">Buat Akun Baru</h2>
                        <p class="text-gray-400 mt-2">Bergabunglah bersama kami dengan mengisi form di bawah.</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="relative mb-6 group">
                            <x-text-input id="name" class="peer" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder=" " />
                            <x-input-label for="name" value="Nama Lengkap" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div class="relative mb-6 group">
                            <x-text-input id="email" class="peer" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder=" " />
                            <x-input-label for="email" value="Alamat Email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="relative mb-6 group">
                            <x-text-input id="password" class="peer" type="password" name="password" required autocomplete="new-password" placeholder=" " />
                            <x-input-label for="password" value="Password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="relative mt-4 group">
                            <x-text-input id="password_confirmation" class="peer" type="password" name="password_confirmation" required autocomplete="new-password" placeholder=" " />
                            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="mt-8">
                            <x-primary-button class="w-full justify-center text-base py-3">
                                {{ __('Daftar') }}
                            </x-primary-button>
                        </div>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-400">
                                Sudah punya akun?
                                <a href="{{ route('login') }}" class="font-medium text-indigo-400 hover:text-indigo-300">
                                    Login di sini
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
