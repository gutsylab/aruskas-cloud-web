<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px;
        }

        .button {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: none;
        }

        .button:hover {
            background-color: #218838;
        }

        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }

        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Verifikasi Email Anda</h1>
        </div>

        <div class="content">
            <h2>Halo, {{ $merchant->name }}!</h2>

            <p>Terima kasih telah mendaftar di {{ config('app.name') }}. Untuk melengkapi proses registrasi, silakan
                verifikasi alamat email Anda dengan mengklik tombol di bawah ini:</p>

            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="button">Verifikasi Email</a>
            </div>

            <div class="info-box">
                <h3>Detail Akun Anda:</h3>
                <ul>
                    <li><strong>Nama Perusahaan:</strong> {{ $merchant->name }}</li>
                    <li><strong>Email:</strong> {{ $merchant->email }}</li>
                    <li><strong>Tenant ID:</strong> {{ $merchant->tenant_id }}</li>
                    <li><strong>URL Akses:</strong> <a
                            href="{{ url('/' . $merchant->tenant_id) }}">{{ url('/' . $merchant->tenant_id) }}</a></li>
                </ul>
            </div>

            <div class="warning">
                <strong>Penting:</strong>
                <ul>
                    <li>Link verifikasi ini akan kedaluwarsa dalam 24 jam</li>
                    <li>Jika Anda tidak dapat mengklik tombol, salin dan tempel URL berikut ke browser Anda:</li>
                </ul>
                <p style="word-break: break-all; font-size: 12px; color: #666;">{{ $verificationUrl }}</p>
            </div>

            <p>Setelah email terverifikasi, Anda dapat login ke aplikasi menggunakan:</p>
            <ul>
                <li><strong>URL:</strong> <a
                        href="{{ url('/' . $merchant->tenant_id) }}">{{ url('/' . $merchant->tenant_id) }}</a></li>
                <li><strong>Email:</strong> {{ $merchant->email }}</li>
                <li><strong>Password:</strong> Password yang Anda buat saat registrasi</li>
            </ul>

            <p>Jika Anda tidak melakukan registrasi ini, silakan abaikan email ini.</p>

            <p>Terima kasih,<br>
                Tim {{ config('app.name') }}</p>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon jangan balas email ini.</p>
            <p>Butuh bantuan? Hubungi support kami di <a
                    href="mailto:{{ config('app.mail_support') }}">{{ config('app.mail_support') }}</a></p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved. By <a
                    href="{{ config('app.website') }}">{{ config('app.organization_name') }}</a></p>
        </div>
    </div>
</body>

</html>
