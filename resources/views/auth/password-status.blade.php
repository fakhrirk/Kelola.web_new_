{{--
    File: resources/views/auth/password-status.blade.php
    Deskripsi: Halaman tunggu yang akan refresh otomatis setiap 10 detik.
--}}
<x-guest-layout>
    {{-- Meta tag untuk refresh otomatis. Browser akan memuat ulang halaman ini setiap 10 detik. --}}
    @push('meta')
        <meta http-equiv="refresh" content="10">
    @endpush

    <div class="text-center p-8">
        <svg class="mx-auto h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">Permintaan Terkirim</h3>
        <div class="mt-2 text-sm text-gray-700">
            <p>Permintaan reset password Anda telah dikirim dan sedang menunggu persetujuan Owner.</p>
            <p class="font-semibold mt-2">Silahkan tunggu...</p>
            <p class="mt-1">Halaman ini akan otomatis beralih setelah permintaan Anda disetujui.</p>
        </div>
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                Kembali ke halaman Login
            </a>
        </div>
    </div>
</x-guest-layout>
