# Laravel Bootstrap 5 Admin Dashboard dengan Sidebar 3 Level

![Laravel](https://img.shields.io/badge/Laravel-12.19.3-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Status](https://img.shields.io/badge/Status-Ready-28a745?style=for-the-badge)

## 🚀 Deskripsi

Project Laravel yang telah dikonversi dari TailwindCSS ke Bootstrap 5 dengan **sidebar responsive** yang mendukung **menu 3 level** (multi-level collapse navigation). Sidebar ini sangat cocok untuk aplikasi admin dashboard yang memerlukan navigasi hierarkis yang kompleks.

## ✨ Fitur Utama

### 🎯 Sidebar 3 Level
- **Level 1**: Menu utama (Dashboard, User Management, dll.)
- **Level 2**: Sub menu dengan collapse animation
- **Level 3**: Sub-sub menu dengan styling berbeda
- **Active State**: Highlight otomatis untuk menu yang sedang aktif
- **Icons**: Menggunakan Font Awesome 6.5.1

### 📱 Responsive Design
- **Desktop**: Sidebar fixed di sisi kiri (280px width)
- **Mobile**: Sidebar tersembunyi, toggle dengan button
- **Auto-hide**: Otomatis tersembunyi saat klik di luar area (mobile)
- **Smooth Animation**: Transisi halus untuk semua interaksi

## 🚀 Quick Start

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Build Assets
```bash
npm run build
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Run Server
```bash
php artisan serve
# atau gunakan script yang disediakan
./start-server.sh
```

### 5. Akses Aplikasi
- **Homepage**: `http://localhost:8000`
- **Admin Dashboard**: `http://localhost:8000/admin/dashboard`
- **User Management**: `http://localhost:8000/admin/users`

## 🗂️ Struktur Menu Sidebar

### 📊 Dashboard
- Link langsung ke dashboard utama

### 👥 User Management
- **All Users** → User listing page
- **Roles & Permissions** (Level 2)
  - Manage Roles (Level 3)
  - Manage Permissions (Level 3)
  - Assign Roles (Level 3)
- **User Profiles** → Profile management

### 📝 Content Management  
- **Posts** (Level 2)
  - All Posts (Level 3)
  - Add New Post (Level 3)
  - Categories (Level 3)
  - Tags (Level 3)
- **Pages** (Level 2)
  - All Pages (Level 3)
  - Add New Page (Level 3)
  - Page Templates (Level 3)
- **Media Library** → File management

### 🛒 E-Commerce
- **Products** (Level 2)
  - All Products (Level 3)
  - Add Product (Level 3)
  - Product Categories (Level 3)
  - Inventory (Level 3)
- **Orders** → Order management
- **Customers** → Customer data

### 📈 Reports & Analytics
- **Sales Reports** → Sales analytics
- **User Analytics** → User behavior
- **Custom Reports** (Level 2)
  - Report Builder (Level 3)
  - Saved Reports (Level 3)
  - Scheduled Reports (Level 3)

### ⚙️ Settings
- **General Settings** → App configuration
- **System Settings** (Level 2)
  - Email Configuration (Level 3)
  - Cache Settings (Level 3)
  - Backup Settings (Level 3)
- **Security** → Security settings

## 📱 Browser Support

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+

## 📄 License

Open source - silakan digunakan untuk project apapun.

---

**Happy Coding!** 🎉

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
