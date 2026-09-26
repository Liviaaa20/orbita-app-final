# ORBITA
## Sistem Manajemen Data Alat dan Histori Operasional Berbasis Web

ORBITA merupakan sistem berbasis web yang dikembangkan untuk mendukung pengelolaan data alat, histori operasional, pemeliharaan, serta permintaan perbaikan pada Stasiun Meteorologi Maritim BMKG Tanjung Emas Semarang.

---

## Teknologi

- Laravel 12
- PHP 8.2+
- MySQL
- Bootstrap 5
- jQuery
- DataTables

---

## Persyaratan

Pastikan perangkat telah terpasang:

- PHP 8.2 atau lebih baru
- Composer
- MySQL
- Node.js & NPM (jika ingin menjalankan Vite)

---

## Instalasi

### 1. Clone Project

```bash
git clone <repository>
cd orbita-app-final
```

### 2. Jalankan Setup

#### Windows

Jalankan file:

```
setup.bat
```

atau melalui CMD:

```cmd
setup.bat
```

#### macOS / Linux

```bash
chmod +x setup.sh
./setup.sh
```

---

## Konfigurasi Database

Buka file `.env` kemudian sesuaikan konfigurasi database.

Contoh:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=orbita_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## Jalankan Migrasi

```bash
php artisan migrate
```

Jika menggunakan data awal:

```bash
php artisan migrate --seed
```

---

## Menjalankan Aplikasi

```bash
php artisan serve
```

Aplikasi dapat diakses melalui:

```
http://127.0.0.1:8000
```

---

## Login

Gunakan akun yang tersedia pada database atau hasil seeder.

---

## Catatan

Jika tidak menggunakan file setup, jalankan perintah berikut secara manual:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan optimize:clear
php artisan migrate
php artisan serve
```

---

## Pengembang

**Felina Devi Maharani**
**Rossa Livia Monika Hardianti**
**Ivana Purbaningtyas**

Program Studi Sistem Informasi

Universitas Semarang