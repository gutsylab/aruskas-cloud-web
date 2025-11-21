<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Akun Selesai</title>
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
            background-color: #28a745;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .success-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .content {
            padding: 30px;
        }

        .button {
            display: inline-block;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 5px;
        }

        .button-primary {
            background-color: #007bff;
        }

        .button-primary:hover {
            background-color: #0056b3;
        }

        .button-success {
            background-color: #28a745;
        }

        .button-success:hover {
            background-color: #218838;
        }

        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }

        .login-box {
            background-color: #f8f9fa;
            border: 2px solid #28a745;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }

        .highlight {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .steps {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .steps ol {
            margin: 0;
            padding-left: 20px;
        }

        .steps li {
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="success-icon">✅</div>
            <h1>Setup Akun Berhasil!</h1>
        </div>

        <div class="content">
            <h2>Selamat Datang, {{ $merchant->name }}! 🎉</h2>

            <p><strong>Kabar baik!</strong> Setup akun tenant Anda telah berhasil diselesaikan. Database Anda telah
                dibuat dan siap digunakan.</p>

            <div class="login-box">
                <h3 style="margin-top: 0; color: #28a745;">📋 Informasi Login Anda:</h3>
                <ul style="list-style: none; padding: 0;">
                    <li><strong>🏢 Nama Perusahaan:</strong> {{ $merchant->name }}</li>
                    <li><strong>🆔 Tenant ID:</strong> <code>{{ $tenantId }}</code></li>
                    <li><strong>📧 Email Login:</strong> <code>{{ $email }}</code></li>
                    <li><strong>🔗 URL Login:</strong> <a href="{{ $loginUrl }}">{{ $loginUrl }}</a></li>
                </ul>
            </div>

            <div class="steps">
                <h3 style="margin-top: 0;">📝 Langkah Selanjutnya:</h3>
                <ol>
                    <li><strong>Verifikasi Email Anda</strong> (Penting untuk keamanan akun)</li>
                    <li>Login ke aplikasi menggunakan kredensial di atas</li>
                    <li>Mulai menggunakan aplikasi</li>
                </ol>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationUrl }}" class="button button-success">✉️ Verifikasi Email Sekarang</a>
                <br>
                <a href="{{ $loginUrl }}" class="button button-primary">🚀 Login ke Dashboard</a>
            </div>

            <div class="info-box">
                <h3 style="margin-top: 0;">ℹ️ Informasi Penting:</h3>
                <ul>
                    <li><strong>Link verifikasi email</strong> akan kedaluwarsa dalam <strong>24 jam</strong></li>
                    <li>Anda dapat login bahkan sebelum verifikasi email, tapi beberapa fitur mungkin terbatas</li>
                    <li>Gunakan password yang Anda buat saat registrasi untuk login</li>
                    <li>Jika mengalami kendala, hubungi tim support kami</li>
                </ul>
            </div>

            <div class="highlight">
                <strong>💡 Tips:</strong> Simpan email ini sebagai referensi untuk informasi login Anda. Pastikan untuk
                mengganti password setelah login pertama kali untuk keamanan tambahan.
            </div>

            <p style="margin-top: 30px;">Jika Anda tidak dapat mengklik tombol di atas, salin dan tempel URL berikut ke
                browser:</p>
            <p
                style="word-break: break-all; font-size: 12px; background-color: #f8f9fa; padding: 10px; border-radius: 5px;">
                <strong>Verifikasi:</strong> {{ $verificationUrl }}<br>
                <strong>Login:</strong> {{ $loginUrl }}
            </p>

            <p>Jika Anda tidak melakukan registrasi ini, silakan abaikan email ini atau hubungi tim support.</p>

            <p style="margin-top: 30px;">Terima kasih telah memilih {{ config('app.name') }}!<br>
                <strong>Tim {{ config('app.name') }}</strong>
            </p>
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
