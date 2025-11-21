# Setup Supervisor untuk Laravel Queue Workers

Panduan ini menjelaskan cara menjalankan Laravel queue workers (termasuk `SetupTenantDatabase` dan `SendTenantEmailVerification` jobs) menggunakan Supervisor.

## 1. Install Supervisor

### macOS
```bash
brew install supervisor
```

### Ubuntu/Debian
```bash
sudo apt-get install supervisor
```

### CentOS/RHEL
```bash
sudo yum install supervisor
```

## 2. Konfigurasi Supervisor

File konfigurasi sudah dibuat di: `supervisor/laravel-queue-worker.conf`

### Untuk Production (Linux Server)

Copy file konfigurasi ke direktori Supervisor:

```bash
sudo cp supervisor/laravel-queue-worker.conf /etc/supervisor/conf.d/
```

**PENTING:** Edit file `/etc/supervisor/conf.d/laravel-queue-worker.conf` dan sesuaikan:
- `command`: Ganti path sesuai lokasi project di server
- `user`: Ganti dengan user yang menjalankan aplikasi (misal: `www-data`, `nginx`, `ubuntu`)
- `stdout_logfile`: Sesuaikan path log file

Contoh untuk production:
```ini
[program:laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/aruskas-cloud/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/aruskas-cloud/storage/logs/queue-worker.log
stopwaitsecs=3600
```

### Untuk Development (macOS)

1. Buat direktori konfigurasi Supervisor (jika belum ada):
```bash
mkdir -p /usr/local/etc/supervisor.d
```

2. Copy atau symlink file konfigurasi:
```bash
ln -s /Users/ianibnuwahab/Development/projects/gutsylab/laravel/aruskas-cloud/supervisor/laravel-queue-worker.conf /usr/local/etc/supervisor.d/
```

3. Edit file `/usr/local/opt/supervisor/supervisord.ini` atau buat jika belum ada:
```ini
[supervisord]
logfile=/usr/local/var/log/supervisord.log
pidfile=/usr/local/var/run/supervisord.pid

[inet_http_server]
port=127.0.0.1:9001

[supervisorctl]
serverurl=http://127.0.0.1:9001

[rpcinterface:supervisor]
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface

[include]
files = /usr/local/etc/supervisor.d/*.conf
```

## 3. Reload Supervisor Configuration

### Production (Linux)
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-queue-worker:*
```

### Development (macOS)
```bash
# Start supervisord jika belum berjalan
brew services start supervisor
# atau
supervisord -c /usr/local/etc/supervisord.ini

# Reload konfigurasi
supervisorctl reread
supervisorctl update
supervisorctl start laravel-queue-worker:*
```

## 4. Perintah Supervisor yang Berguna

### Cek Status Workers
```bash
supervisorctl status
```

### Start Workers
```bash
supervisorctl start laravel-queue-worker:*
```

### Stop Workers
```bash
supervisorctl stop laravel-queue-worker:*
```

### Restart Workers (setelah deploy atau code changes)
```bash
supervisorctl restart laravel-queue-worker:*
```

### Stop dan Start Supervisord
```bash
# macOS
brew services restart supervisor

# Linux
sudo systemctl restart supervisor
```

### Lihat Log
```bash
supervisorctl tail -f laravel-queue-worker:laravel-queue-worker_00 stdout
# atau langsung
tail -f storage/logs/queue-worker.log
```

## 5. Konfigurasi Queue di Laravel

Pastikan file `.env` sudah dikonfigurasi dengan benar:

```env
QUEUE_CONNECTION=database
# atau gunakan redis untuk performa lebih baik
# QUEUE_CONNECTION=redis

# Jika menggunakan Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Jika menggunakan database queue, jalankan migrasi:
```bash
php artisan queue:table
php artisan migrate
```

## 6. Monitoring

### Cek Queue Stats
```bash
php artisan queue:monitor
```

### Lihat Failed Jobs
```bash
php artisan queue:failed
```

### Retry Failed Jobs
```bash
# Retry specific job
php artisan queue:retry {job-id}

# Retry all failed jobs
php artisan queue:retry all
```

### Clear Failed Jobs
```bash
php artisan queue:flush
```

## 7. Penjelasan Konfigurasi

```ini
process_name=%(program_name)s_%(process_num)02d  # Nama proses dengan nomor
command=php artisan queue:work                    # Perintah yang dijalankan
  --sleep=3                                       # Tunggu 3 detik jika queue kosong
  --tries=3                                       # Retry job 3x jika gagal
  --max-time=3600                                 # Restart worker setiap 1 jam
autostart=true                                    # Auto start saat supervisor start
autorestart=true                                  # Auto restart jika crash
numprocs=2                                        # Jumlah worker (paralel)
stopwaitsecs=3600                                 # Tunggu max 1 jam saat stop
```

## 8. Best Practices

### Production
- Gunakan minimal 2-4 workers (`numprocs=4`)
- Gunakan Redis untuk queue connection (lebih cepat dari database)
- Set `max-time` ke 3600 (1 jam) untuk restart otomatis
- Monitor log secara berkala
- Setup alert untuk failed jobs

### Development
- 1-2 workers sudah cukup (`numprocs=1`)
- Database queue sudah cukup untuk development
- Restart worker setiap kali code changes:
  ```bash
  supervisorctl restart laravel-queue-worker:*
  ```

## 9. Troubleshooting

### Workers tidak berjalan
```bash
# Cek status
supervisorctl status

# Cek log supervisord
tail -f /usr/local/var/log/supervisord.log  # macOS
tail -f /var/log/supervisor/supervisord.log # Linux

# Cek log worker
tail -f storage/logs/queue-worker.log
```

### Job tidak diproses
```bash
# Cek apakah ada job di queue
php artisan queue:monitor

# Cek failed jobs
php artisan queue:failed

# Restart workers
supervisorctl restart laravel-queue-worker:*
```

### Permission error
```bash
# Pastikan user supervisor memiliki akses ke project
sudo chown -R www-data:www-data /var/www/aruskas-cloud
sudo chmod -R 755 /var/www/aruskas-cloud/storage
```

## 10. Alternative: Menggunakan systemd (Linux only)

Jika tidak ingin menggunakan Supervisor, bisa gunakan systemd:

```bash
# Buat file /etc/systemd/system/laravel-queue.service
sudo nano /etc/systemd/system/laravel-queue.service
```

Isi file:
```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/aruskas-cloud
ExecStart=/usr/bin/php /var/www/aruskas-cloud/artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always

[Install]
WantedBy=multi-user.target
```

Jalankan:
```bash
sudo systemctl daemon-reload
sudo systemctl enable laravel-queue
sudo systemctl start laravel-queue
sudo systemctl status laravel-queue
```

## 11. Horizon (Alternative untuk Redis)

Jika menggunakan Redis, pertimbangkan Laravel Horizon untuk monitoring yang lebih baik:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

Supervisor config untuk Horizon:
```ini
[program:laravel-horizon]
process_name=%(program_name)s
command=php /path/to/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/horizon.log
stopwaitsecs=3600
```
