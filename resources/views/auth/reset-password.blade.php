<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
        <div class="w-full max-w-md mx-auto bg-white rounded-2xl shadow-xl p-8">
            <div class="text-left mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Atur Password Baru</h2>
                <p class="text-gray-500 mt-2">Permintaan Anda telah disetujui. Silakan masukkan password baru Anda.</p>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-6">
                    <x-input-label for="password" value="Password Baru" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="mb-6">
                    <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                </div>

                <x-primary-button class="w-full justify-center text-base py-3">
                    {{ __('Simpan Password Baru') }}
                </x-primary-button>
            </form>
        </div>
    </div>
</x-guest-layout>
