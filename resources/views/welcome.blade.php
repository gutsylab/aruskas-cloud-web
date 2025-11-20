<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arus Kas Cloud - Platform Akuntansi untuk Semua</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Hero Section -->
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-4xl mx-auto text-center px-4">
            <!-- Logo/Title -->
            <h1 class="text-5xl font-bold text-gray-900 mb-4">
                Arus Kas Cloud
            </h1>
            <p class="text-xl text-gray-600 mb-8">
                Platform Akuntansi Sederhana untuk Bisnis Anda
            </p>

            <!-- Features -->
            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Mudah Digunakan</h3>
                    <p class="text-gray-600">Dirancang untuk pengguna tanpa latar belakang akuntansi</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Akses Dimana Saja</h3>
                    <p class="text-gray-600">Kelola keuangan bisnis Anda dari mana saja, kapan saja</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Laporan Lengkap</h3>
                    <p class="text-gray-600">Arus kas, laba rugi, dan laporan keuangan lainnya</p>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="space-y-4 md:space-y-0 md:space-x-4 md:flex md:justify-center">
                <a href="{{ route('tenant.register') }}"
                   class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                    Mulai Sekarang
                </a>
                <a href="#demo"
                   class="inline-block border border-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition duration-200">
                    Lihat Demo
                </a>
            </div>

            <!-- Info -->
            <div class="mt-12 text-center">
                <p class="text-gray-600 mb-4">
                    Sudah memiliki akun bisnis?
                </p>
                <p class="text-sm text-gray-500">
                    Akses akun Anda di: <code class="bg-gray-200 px-2 py-1 rounded">{{ asset('/') }}ID_BISNIS_ANDA</code>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
