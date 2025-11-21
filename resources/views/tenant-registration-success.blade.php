<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berhasil - ArusKAS Cloud</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl w-full mx-4">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-6">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <!-- Success Message -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Selamat Datang di ArusKAS Cloud!</h1>
            <p class="text-lg text-gray-600 mb-4">Akun bisnis Anda telah berhasil dibuat.</p>

            <!-- Setup Processing Notice -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-blue-400 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <div class="text-left">
                        <h3 class="text-sm font-medium text-blue-800">Setup Database Sedang Berjalan</h3>
                        <p class="text-sm text-blue-700 mt-1">
                            Database tenant Anda sedang dikonfigurasi. Anda akan menerima email konfirmasi di <strong>{{ $adminEmail }}</strong> setelah setup selesai (sekitar 1-2 menit).
                        </p>
                    </div>
                </div>
            </div>

            <!-- Account Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Akun</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Perusahaan:</span>
                        <span class="font-medium">{{ $merchant->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID Tenant:</span>
                        <span class="font-mono text-sm bg-gray-200 px-2 py-1 rounded">{{ $merchant->tenant_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email Admin:</span>
                        <span class="font-medium">{{ $adminEmail }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Paket:</span>
                        <span class="font-medium">{{ $plan->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">URL Toko Anda:</span>
                        <span class="font-medium text-blue-600">{{ $tenantUrl }}</span>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">Langkah Selanjutnya</h3>
                <div class="text-left space-y-3 text-blue-800">
                    <p><strong>1. Tunggu email konfirmasi:</strong> Anda akan menerima email setelah setup database selesai</p>
                    <p><strong>2. Verifikasi email Anda:</strong> Cek inbox dan klik link verifikasi</p>
                    <p><strong>3. Akses toko Anda:</strong> Setelah verifikasi, kunjungi <a href="{{ $tenantUrl }}" class="underline">{{ $tenantUrl }}</a></p>
                    <p><strong>4. Login dengan kredensial Anda:</strong> Gunakan email (<strong>{{ $adminEmail }}</strong>) dan password untuk masuk</p>
                    <p><strong>5. Setup toko Anda:</strong> Konfigurasi produk dan pengaturan</p>
                    <p><strong>6. Mulai berjualan:</strong> Sistem POS Anda siap digunakan!</p>
                </div>
            </div>

            @if($plan->trial_days > 0)
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8">
                    <p class="text-green-800">
                        <strong>Uji Coba Gratis:</strong> Anda memiliki {{ $plan->trial_days }} hari untuk menjelajahi semua fitur tanpa biaya.
                    </p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="space-y-4">
                <a href="{{ route('tenant.email.resend.form') }}"
                   class="inline-block w-full bg-blue-600 text-white py-3 px-6 rounded-md font-semibold hover:bg-blue-700 transition duration-200">
                    Belum menerima email? Kirim Ulang Verifikasi
                </a>

                <a href="{{ $tenantUrl }}"
                   class="inline-block w-full border border-gray-300 text-gray-700 py-3 px-6 rounded-md font-semibold hover:bg-gray-50 transition duration-200">
                    Menuju Toko (setelah verifikasi)
                </a>

                <a href="/"
                   class="inline-block w-full border border-gray-300 text-gray-700 py-3 px-6 rounded-md font-semibold hover:bg-gray-50 transition duration-200">
                    Kembali ke Beranda
                </a>
            </div>

            <!-- Support Info -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Butuh bantuan untuk memulai?
                    <a href="mailto:support@aruskas.com" class="text-blue-600 hover:underline">Hubungi tim support kami</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
