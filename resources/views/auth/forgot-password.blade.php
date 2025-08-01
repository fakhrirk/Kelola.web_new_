<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
        <div class="w-full max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-2 bg-white rounded-2xl shadow-xl overflow-hidden">

            {{-- Kolom Informasi --}}
            <div class="hidden lg:flex flex-col justify-center items-center p-12 bg-gray-800 text-white text-center">
                <div class="relative z-10">
                    <h1 class="text-4xl font-bold tracking-tight">Kelola.web</h1>
                    <p class="mt-4 text-lg text-gray-300">Manajemen sumber daya manusia yang efisien dan modern.</p>
                </div>
            </div>

            {{-- Kolom Form --}}
            <div class="p-8 sm:p-12 flex flex-col justify-center">
                <div class="w-full">

                    {{-- Tampilkan pesan status jika ada (setelah redirect) --}}
                    @if (session('status'))
                        <div class="text-center p-6 bg-blue-50 border border-blue-200 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Permintaan Terkirim</h3>
                            <div class="mt-2 text-sm text-gray-700">
                                <p>{{ session('status') }}</p>
                                <p class="font-semibold mt-1">Silakan periksa email Anda atau tunggu persetujuan dari Owner untuk melanjutkan.</p>
                            </div>
                        </div>
                    @else
                        {{-- Form Lupa Password --}}
                        <div>
                            <div class="text-left mb-8">
                                <p class="text-gray-600">
                                    {{ __('Tidak masalah. Cukup beritahu kami alamat email Anda dan kami akan memulai proses reset password dengan persetujuan Owner.') }}
                                </p>
                            </div>

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                {{-- Input Email --}}
                                <div>
                                    <label for="email" class="text-sm font-medium text-gray-700">{{ __('Alamat Email') }}</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                           class="block w-full px-4 py-3 mt-1 text-gray-900 bg-gray-50 border rounded-md transition
                                                  {{ $errors->has('email')
                                                      ? 'border-red-500 focus:ring-red-500 focus:border-red-500'
                                                      : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' }}">

                                    {{-- Pesan Error Validasi --}}
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mt-8">
                                    <button type="submit"
                                            class="w-full flex justify-center items-center text-center py-3 px-4 border border-transparent bg-gray-800 text-white font-bold rounded-md hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700 transition-colors duration-300">
                                        {{ __('KIRIM PERMINTAAN RESET') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            Kembali ke halaman Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
