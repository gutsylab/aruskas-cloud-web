<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Terverifikasi - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .verification-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }
        .success-icon {
            font-size: 4rem;
            color: #28a745;
        }
        .already-verified-icon {
            font-size: 4rem;
            color: #ffc107;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
        }
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            padding: 12px 30px;
            border-radius: 25px;
        }
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <div class="verification-card">
        <div class="card-body p-5 text-center">
            @if($status === 'verified')
                <i class="bi bi-check-circle-fill success-icon mb-4"></i>
                <h1 class="h2 mb-4 text-success">Email Berhasil Diverifikasi!</h1>
            @else
                <i class="bi bi-exclamation-triangle-fill already-verified-icon mb-4"></i>
                <h1 class="h2 mb-4 text-warning">Email Sudah Terverifikasi</h1>
            @endif
            
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                {{ $message }}
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-building me-2 text-primary"></i>
                                Detail Akun
                            </h5>
                            <p class="card-text">
                                <strong>Perusahaan:</strong><br>
                                {{ $merchant->name }}
                            </p>
                            <p class="card-text">
                                <strong>Email:</strong><br>
                                {{ $merchant->email }}
                            </p>
                            <p class="card-text">
                                <strong>Tenant ID:</strong><br>
                                {{ $merchant->tenant_id }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 bg-light h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-calendar-check me-2 text-success"></i>
                                Status Verifikasi
                            </h5>
                            <p class="card-text">
                                <strong>Tanggal Verifikasi:</strong><br>
                                {{ $merchant->email_verified_at ? $merchant->email_verified_at->format('d M Y, H:i') : 'Belum terverifikasi' }}
                            </p>
                            <p class="card-text">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Email Terverifikasi
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                @if(isset($tenantUrl))
                    <a href="{{ $tenantUrl }}" class="btn btn-primary btn-lg me-md-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Masuk ke Aplikasi
                    </a>
                @endif
                <a href="{{ url('/') }}" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-house me-2"></i>
                    Kembali ke Beranda
                </a>
            </div>
            
            <div class="mt-4 pt-4 border-top">
                <p class="text-muted small">
                    <i class="bi bi-shield-check me-1"></i>
                    Email Anda telah berhasil diverifikasi. Sekarang Anda dapat menggunakan semua fitur yang tersedia.
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
