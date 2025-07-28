<x-app-layout>
    {{-- Header Halaman --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Dashboard Absensi') }}
        </h2>
    </x-slot>

    {{-- Konten Utama --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Kolom Peta -->
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700">
                    <h3 class="text-xl font-semibold mb-4 text-white">Lokasi Anda Saat Ini</h3>
                    <div id="map" class="h-[350px] rounded-md z-10 bg-gray-700"></div>
                    <div id="location-status" class="mt-4 text-center font-semibold p-3 rounded-md bg-gray-700 transition-colors duration-300">
                        Memeriksa lokasi...
                    </div>
                </div>

                <!-- Kolom Absensi -->
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700 flex flex-col justify-center items-center">
                    <h3 class="text-xl font-semibold mb-2 text-white">Presensi Hari Ini</h3>
                    <p class="text-gray-400 mb-6 text-2xl font-mono" id="current-time">Memuat jam...</p>
                    <div class="w-full space-y-4">
                        <button id="clock-in-btn" disabled class="w-full text-lg bg-green-600 text-white font-bold py-4 px-4 rounded-md transition duration-300 disabled:bg-gray-600 disabled:cursor-not-allowed flex items-center justify-center space-x-2">
                            <span>Clock In</span>
                        </button>
                        <button id="clock-out-btn" disabled class="w-full text-lg bg-red-600 text-white font-bold py-4 px-4 rounded-md transition duration-300 disabled:bg-gray-600 disabled:cursor-not-allowed flex items-center justify-center space-x-2">
                            <span>Clock Out</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Menambahkan Aset CSS dan JS untuk Peta --}}
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const officeLat = {{ config('app.office_latitude', -6.7319469) }};
            const officeLng = {{ config('app.office_longitude', 108.5568559) }};
            const officeRadius = {{ config('app.office_radius', 10) }};
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const timeEl = document.getElementById('current-time');
                const statusEl = document.getElementById('location-status');
                const clockInBtn = document.getElementById('clock-in-btn');
                const clockOutBtn = document.getElementById('clock-out-btn');

                let userPosition = { lat: 0, lng: 0 };

                setInterval(() => {
                    timeEl.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                }, 1000);

                const map = L.map('map').setView([officeLat, officeLng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                L.marker([officeLat, officeLng]).addTo(map).bindPopup('<b>Lokasi Kantor</b>');
                const officeCircle = L.circle([officeLat, officeLng], {
                    color: 'red', fillColor: '#f03', fillOpacity: 0.2, radius: officeRadius
                }).addTo(map);

                let userMarker;

                function onLocationFound(e) {
                    userPosition = { lat: e.latlng.lat, lng: e.latlng.lng };
                    if (!userMarker) {
                        userMarker = L.marker(e.latlng).addTo(map).bindPopup("Posisi Anda").openPopup();
                    } else {
                        userMarker.setLatLng(e.latlng);
                    }

                    const distance = map.distance(e.latlng, officeCircle.getLatLng());
                    const isInArea = distance < officeRadius;

                    if (isInArea) {
                        statusEl.textContent = 'Anda berada di dalam area presensi.';
                        statusEl.className = 'mt-4 text-center font-semibold p-3 rounded-md bg-green-500 text-white transition-colors duration-300';
                        officeCircle.setStyle({ color: 'green', fillColor: '#22c55e' });
                        clockInBtn.disabled = false;
                        clockOutBtn.disabled = false;
                    } else {
                        statusEl.textContent = `Anda di luar area presensi. Jarak: ${Math.round(distance)}m`;
                        statusEl.className = 'mt-4 text-center font-semibold p-3 rounded-md bg-red-500 text-white transition-colors duration-300';
                        officeCircle.setStyle({ color: 'red', fillColor: '#f03' });
                        clockInBtn.disabled = true;
                        clockOutBtn.disabled = true;
                    }
                }

                function onLocationError(e) {
                    statusEl.textContent = 'Gagal dapatkan lokasi. Aktifkan GPS & izinkan akses.';
                    statusEl.className = 'mt-4 text-center font-semibold p-3 rounded-md bg-yellow-500 text-black transition-colors duration-300';
                }

                map.on('locationfound', onLocationFound);
                map.on('locationerror', onLocationError);
                map.locate({ setView: true, maxZoom: 17, watch: true, enableHighAccuracy: true });

                async function sendAttendanceRequest(url) {
                    clockInBtn.disabled = true;
                    clockOutBtn.disabled = true;
                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                latitude: userPosition.lat,
                                longitude: userPosition.lng
                            })
                        });
                        const data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Terjadi kesalahan.');
                        alert(data.message);
                    } catch (error) {
                        console.error('Attendance Error:', error);
                        alert('Error: ' + error.message);
                    } finally {
                        // Aktifkan kembali tombol setelah beberapa saat jika masih di dalam area
                        setTimeout(() => onLocationFound({ latlng: L.latLng(userPosition.lat, userPosition.lng) }), 1000);
                    }
                }

                clockInBtn.addEventListener('click', () => sendAttendanceRequest('/api/attendance/clock-in'));
                clockOutBtn.addEventListener('click', () => sendAttendanceRequest('/api/attendance/clock-out'));
            });
        </script>
    @endpush
</x-app-layout>
