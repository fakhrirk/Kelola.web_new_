<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
        <div class="w-full max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-2 bg-white rounded-2xl shadow-xl overflow-hidden">

            <div class="hidden lg:flex flex-col justify-center items-center p-12 bg-gray-800 text-white text-center">
                <div class="relative z-10">
                    <h1 class="text-4xl font-bold tracking-tight">Kelola.web</h1>
                    <p class="mt-4 text-lg text-gray-300">Manajemen sumber daya manusia yang efisien dan modern.</p>
                </div>
            </div>

            <div class="p-8 sm:p-12 flex flex-col justify-center" x-data="{
                    submitted: false,
                    isLoading: false,
                    statusMessage: '',
                    errorMessage: '',
                    email: '{{ old('email') }}',
                    token: '',

                    submitRequest() {
                        this.errorMessage = '';
                        this.isLoading = true;

                        // Ambil CSRF token dari meta tag. Guest layout memastikannya ada.
                        const csrfToken = document.querySelector('meta[name=csrf-token]').getAttribute('content');

                        fetch('/forgot-password', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ email: this.email })
                        })
                        .then(response => {
                            if (!response.ok) {
                                // Jika response error, lempar error beserta statusnya untuk ditangani di catch
                                return response.json().then(err => {
                                    err.status = response.status; // Tambahkan status code ke error object
                                    throw err;
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            this.submitted = true;
                            this.isLoading = false;
                            this.statusMessage = data.status;
                            this.token = data.token;
                            this.startPolling();
                        })
                        .catch(err => {
                            this.isLoading = false;
                            // PERBAIKAN: Penanganan error yang lebih baik
                            if (err.status === 422 && err.errors) {
                                // Error validasi dari Laravel
                                this.errorMessage = Object.values(err.errors)[0][0];
                            } else if (err.status === 419) {
                                // Error CSRF Token
                                this.errorMessage = 'Sesi Anda telah berakhir. Silakan segarkan halaman dan coba lagi.';
                            } else {
                                // Error umum lainnya
                                this.errorMessage = 'Terjadi kesalahan server. Pastikan email terdaftar dan coba lagi nanti.';
                            }
                        });
                    },

                    startPolling() {
                        let interval = setInterval(() => {
                            fetch(`/password-reset-status/${this.token}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'approved') {
                                    clearInterval(interval);
                                    window.location.href = `/reset-password/${this.token}`;
                                }
                            });
                        }, 5000);
                    }
                }">

                <div class="w-full">
                    <div x-show.transition.opacity.duration.500ms="!submitted">
                        <div class="text-left mb-8">
                            <p class="text-gray-600">
                                {{ __('Tidak masalah. Cukup beritahu kami alamat email Anda dan kami akan memulai proses reset password dengan persetujuan Owner.') }}
                            </p>
                        </div>

                        {{-- Pesan error akan muncul di sini --}}
                        <div x-show="errorMessage" x-text="errorMessage" class="mb-4 p-3 text-sm text-red-700 bg-red-100 rounded-lg" x-cloak></div>

                        {{-- Form --}}
                        <form @submit.prevent="submitRequest">
                            <div>
                                <label for="email" class="text-sm font-medium text-gray-700">Alamat Email</label>
                                <input id="email" type="email" x-model="email" required autofocus
                                       class="block w-full px-4 py-3 mt-1 text-gray-900 bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            </div>

                            <div class="mt-8">
                                <button type="submit" :disabled="isLoading"
                                        class="w-full flex justify-center items-center text-center py-3 px-4 border border-transparent bg-gray-800 text-white font-bold rounded-md hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700 transition-colors duration-300 disabled:bg-gray-400">
                                        <span x-show="!isLoading">KIRIM PERMINTAAN RESET</span>
                                        <span x-show="isLoading" x-cloak>Mengirim...</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Tampilan setelah submit berhasil --}}
                    <div x-show="submitted" class="text-center p-6 bg-blue-50 border border-blue-200 rounded-lg" x-cloak>
                        <svg class="mx-auto h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900">Permintaan Terkirim</h3>
                        <div class="mt-2 text-sm text-gray-700">
                            <p x-text="statusMessage"></p>
                            <p class="font-semibold mt-1">Halaman ini akan otomatis beralih setelah Owner menyetujui.</p>
                        </div>
                    </div>

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
