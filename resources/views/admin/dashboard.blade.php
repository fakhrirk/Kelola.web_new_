<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Welcome Message -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-100">
                    <h3 class="text-lg font-medium">Selamat Datang Kembali, {{ Auth::user()->name }}!</h3>
                    <p class="mt-1 text-gray-400">Anda login sebagai **ADMIN**. Kelola data dan pantau aktivitas dari sini.</p>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                {{-- Card 1: Total Karyawan --}}
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700">
                    <h4 class="text-sm font-medium text-gray-400">Total Karyawan</h4>
                    <p class="text-3xl font-bold text-white mt-2">{{ $totalKaryawan ?? 'N/A' }}</p>
                </div>

                {{-- Card 2: Hadir Hari Ini --}}
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700">
                    <h4 class="text-sm font-medium text-gray-400">Hadir Hari Ini</h4>
                    <p class="text-3xl font-bold text-white mt-2">{{ $hadirHariIni ?? 'N/A' }}</p>
                </div>

                {{-- Card 3: Terlambat Hari Ini --}}
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700">
                    <h4 class="text-sm font-medium text-gray-400">Total Absensi Bulan Ini</h4>
                    <p class="text-3xl font-bold text-white mt-2">{{ $absensiBulanIni ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Tabel Aktivitas Absensi Terbaru -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Aktivitas Absensi Terbaru</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-gray-300">
                            <thead class="text-xs text-gray-300 uppercase bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama Karyawan</th>
                                    <th scope="col" class="px-6 py-3">Tanggal</th>
                                    <th scope="col" class="px-6 py-3">Jam Masuk</th>
                                    <th scope="col" class="px-6 py-3">Jam Pulang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($absensiTerbaru ?? [] as $absensi)
                                    <tr class="border-b border-gray-700 hover:bg-gray-700/50">
                                        <td class="px-6 py-4 font-medium text-white whitespace-nowrap">{{ $absensi->user->name ?? 'User Dihapus' }}</td>
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($absensi->attendance_date)->isoFormat('D MMMM YYYY') }}</td>
                                        <td class="px-6 py-4">{{ $absensi->clock_in }}</td>
                                        <td class="px-6 py-4">{{ $absensi->clock_out ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-gray-400">
                                            Belum ada aktivitas absensi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>
</x-app-layout>
