<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Owner Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700">
                    <h4 class="text-sm font-medium text-gray-400">Total Karyawan & Admin</h4>
                    <p class="text-3xl font-bold text-white mt-2">{{ $totalKaryawan ?? 'N/A' }}</p>
                </div>
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700">
                    <h4 class="text-sm font-medium text-gray-400">Total Admin</h4>
                    <p class="text-3xl font-bold text-white mt-2">{{ $totalAdmin ?? 'N/A' }}</p>
                </div>
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700">
                    <h4 class="text-sm font-medium text-gray-400">Hadir Hari Ini</h4>
                    <p class="text-3xl font-bold text-white mt-2">{{ $hadirHariIni ?? 'N/A' }}</p>
                </div>
            </div>

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

            <!-- Grafik Kehadiran Karyawan -->
            <div class="bg-gray-800 overflow-hidden mt-6 shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Grafik Kehadiran (30 Hari Terakhir)</h3>
                    <div class="h-80">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>

<div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Permintaan Reset Password</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-gray-300">
                            <thead class="text-xs text-gray-300 uppercase bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3">Email</th>
                                    <th class="px-6 py-3">Tanggal Permintaan</th>
                                    <th class="px-6 py-3">Aksi</th>
                                </tr>
                            </thead>        
                            <tbody>
                                @forelse ($passwordRequests as $request)
                                    <tr class="border-b border-gray-700">
                                        <td class="px-6 py-4">{{ $request->email }}</td>
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($request->created_at)->isoFormat('D MMM YYYY, HH:mm') }}</td>
                                        <td class="px-6 py-4">
                                            {{-- PERBAIKAN ADA DI SINI --}}
                                            {{-- Pastikan form memiliki method="POST" dan @csrf --}}
                                            <form action="{{ route('owner.password.reset.approve', $request->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="font-medium text-green-400 hover:text-green-300">Setujui</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-6 text-gray-400">Tidak ada permintaan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
</div>

</div>

            {{-- Anda bisa menambahkan tabel lain di sini, seperti rekap gaji, dll. --}}

        </div>
    </div>

    @push('scripts')
    {{-- Memuat library Chart.js dari CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            fetch("{{ route('owner.api.attendance.chart') }}")
                .then(response => response.json())
                .then(apiData => {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: apiData.labels,
                            datasets: [{
                                label: 'Jumlah Kehadiran',
                                data: apiData.data,
                                fill: true,
                                backgroundColor: 'rgba(139, 92, 246, 0.2)',
                                borderColor: 'rgba(139, 92, 246, 1)',
                                tension: 0.3,
                                pointBackgroundColor: 'rgba(139, 92, 246, 1)',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, ticks: { color: '#9ca3af', stepSize: 1 }, grid: { color: 'rgba(255, 255, 255, 0.1)' } },
                                x: { ticks: { color: '#9ca3af' }, grid: { display: false } }
                            },
                            plugins: { legend: { display: false } }
                        }
                    });
                })
                .catch(error => console.error('Error fetching chart data:', error));
        });
    </script>
    @endpush
</x-app-layout>
