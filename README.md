# GutsyMail API 📧

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![API](https://img.shields.io/badge/API-Mail_Service-00D9FF?style=for-the-badge&logo=api&logoColor=white)
![Status](https://img.shields.io/badge/Status-Active-28a745?style=for-the-badge)

## 🚀 Deskripsi

GutsyMail API adalah aplikasi Laravel yang menyediakan layanan pengiriman email melalui REST API. Aplikasi ini memungkinkan client untuk mengirim email baik secara langsung maupun melalui sistem antrian (queue), dengan sistem tracking status pengiriman yang lengkap.

## ✨ Fitur Utama

- 📤 **Pengiriman Email Queue**: Mengirim email melalui sistem antrian untuk performa optimal
- ⚡ **Pengiriman Email Langsung**: Mengirim email secara instant tanpa antrian
- 📊 **Status Tracking**: Memantau status pengiriman email secara real-time
- 🔐 **API Key Authentication**: Sistem autentikasi yang aman dengan API Key
- 📈 **Multiple Email Providers**: Dukungan untuk berbagai provider email
- 🚀 **High Performance**: Dioptimalkan untuk throughput tinggi

## � API Endpoints

### 1. 📤 Send Email (Queue)
```
POST /api/send
```
Mengirim email melalui sistem antrian untuk memastikan performa aplikasi tetap optimal.

**Headers:**
```
Content-Type: application/json
X-Api-Key: YOUR_API_KEY
```

**Body:**
```json
{
  "to": "recipient@example.com",
  "subject": "Subject Email",
  "message": "Isi pesan email",
  "from_name": "Nama Pengirim",
  "from_email": "sender@example.com"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Email has been queued for sending",
  "message_id": "uuid-message-id",
  "queue_status": "pending"
}
```

### 2. ⚡ Send Email (Immediate)
```
POST /api/send-now
```
Mengirim email secara langsung tanpa melalui sistem antrian.

**Headers:**
```
Content-Type: application/json
X-Api-Key: YOUR_API_KEY
```

**Body:**
```json
{
  "to": "recipient@example.com",
  "subject": "Subject Email",
  "message": "Isi pesan email",
  "from_name": "Nama Pengirim",
  "from_email": "sender@example.com"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Email sent successfully",
  "message_id": "uuid-message-id",
  "sent_at": "2025-01-22T10:30:00Z"
}
```

### 3. � Check Email Status
```
GET /api/messages/{id}
```
Melihat status email yang telah dikirim berdasarkan message ID.

**Headers:**
```
X-Api-Key: YOUR_API_KEY
```

**Response:**
```json
{
  "message_id": "uuid-message-id",
  "status": "sent|pending|failed|delivered",
  "to": "recipient@example.com",
  "subject": "Subject Email",
  "sent_at": "2025-01-22T10:30:00Z",
  "delivered_at": "2025-01-22T10:31:15Z",
  "provider": "smtp",
  "error_message": null
}
```

## 🔧 Installation & Setup

### 1. Clone Repository
```bash
git clone https://github.com/iansaimima/gutsymail-api.git
cd gutsymail-api
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 5. Queue Configuration
Pastikan konfigurasi queue di `.env`:
```env
QUEUE_CONNECTION=database
# atau redis untuk performa lebih baik
# QUEUE_CONNECTION=redis
```

### 6. Start Services
```bash
# Start web server
php artisan serve

# Start queue worker (di terminal terpisah)
php artisan queue:work

# Atau gunakan script yang disediakan
./start-server.sh
```

## � API Key Management

### Membuat API Key untuk Client Baru
```bash
php artisan apikey:issue [nama-client]
```

**Contoh:**
```bash
php artisan apikey:issue acme-company
```

**Output:**
```
API Key berhasil dibuat untuk client: acme-company
API Key: ak_1234567890abcdef1234567890abcdef
```

### Menggunakan API Key
Sertakan API Key dalam header setiap request:
```
X-Api-Key: ak_1234567890abcdef1234567890abcdef
```

## 📊 Status Email

| Status | Deskripsi |
|--------|-----------|
| `pending` | Email dalam antrian, belum diproses |
| `processing` | Email sedang dalam proses pengiriman |
| `sent` | Email berhasil dikirim |
| `delivered` | Email berhasil diterima recipient |
| `failed` | Email gagal dikirim |
| `bounced` | Email ditolak oleh server penerima |

## 🔄 Queue Management

### Memantau Queue
```bash
# Lihat jumlah job dalam queue
php artisan queue:size

# Monitor queue secara real-time
php artisan queue:monitor

# Restart queue workers
php artisan queue:restart
```

### Failed Jobs
```bash
# Lihat failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {id}

# Retry semua failed jobs
php artisan queue:retry all
```

## 📈 Monitoring & Logging

### Log Files
- **Application Log**: `storage/logs/laravel.log`
- **Email Log**: `storage/logs/email.log`
- **Queue Log**: `storage/logs/queue.log`

### Performance Monitoring
```bash
# Monitor memory usage
php artisan queue:work --memory=512

# Monitor dengan timeout
php artisan queue:work --timeout=60
```

## 🛠️ Development

### Running Tests
```bash
# Run semua tests
php artisan test

# Run specific test
php artisan test --filter EmailSendingTest
```

### Code Quality
```bash
# PHP CS Fixer
./vendor/bin/php-cs-fixer fix

# PHPStan
./vendor/bin/phpstan analyse
```

## 📚 Response Codes

| Code | Status | Deskripsi |
|------|--------|-----------|
| 200 | OK | Request berhasil |
| 201 | Created | Email berhasil dibuat dan dikirim/diqueue |
| 400 | Bad Request | Parameter request tidak valid |
| 401 | Unauthorized | API Key tidak valid atau tidak ada |
| 404 | Not Found | Message ID tidak ditemukan |
| 422 | Unprocessable Entity | Validasi gagal |
| 429 | Too Many Requests | Rate limit terlampaui |
| 500 | Internal Server Error | Error server |

## 🔧 Configuration

### Email Providers
Edit file `config/mail.php` untuk mengkonfigurasi provider email:

```php
'mailers' => [
    'smtp' => [
        'transport' => 'smtp',
        'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
        'port' => env('MAIL_PORT', 587),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
    ],
],
```

### Rate Limiting
Edit `config/services.php` untuk rate limiting:

```php
'rate_limiting' => [
    'emails_per_minute' => 60,
    'emails_per_hour' => 1000,
],
```

## 🤝 Contributing

1. Fork repository
2. Buat feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buat Pull Request

## 📄 License

Project ini menggunakan [MIT License](LICENSE).

## 📞 Support

Untuk support dan pertanyaan:
- 📧 Email: support@gutsylab.com
- 🐛 Issues: [GitHub Issues](https://github.com/iansaimima/gutsymail-api/issues)
- 📖 Documentation: [Wiki](https://github.com/iansaimima/gutsymail-api/wiki)

---

**Developed with ❤️ by GutsyLab Team**
