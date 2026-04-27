# 🏫 SIAKAD SMP MUARA INDONESIA

Sistem Informasi Akademik (SIAKAD) untuk SMP, dibangun menggunakan Laravel, Filament Admin, Livewire, dan Tailwind CSS.

## 🛠️ Persyaratan Sistem (Prerequisites)
Sebelum install, pastikan laptop kamu sudah terpasang:
* **PHP** (Minimal versi 8.2 atau pakai Laravel Herd)
* **Composer**
* **Node.js & NPM**
* **Git**

## 🚀 Cara Instalasi (Langkah demi Langkah)

**1. Clone Repository**
Buka terminal dan jalankan perintah ini untuk mengunduh project:
```bash
git clone [https://github.com/username-kamu/siakad-smp.git](https://github.com/username-kamu/siakad-smp.git)
cd siakad-smp

STEP BY STEP
1. Install Dependencies Backend (PHP)
Bash
composer install

2. Setup Environment variables
Copy file konfigurasi bawaan dan ubah namanya:
Untuk Windows (CMD/PowerShell): copy .env.example .env
Untuk Mac/Linux: cp .env.example .env

Setelah itu, buat App Key rahasia untuk keamanan:
Bash
php artisan key:generate

3. Setup Database (SQLite)
Buka file .env yang baru saja dibuat, lalu pastikan konfigurasi database-nya seperti ini (hapus konfigurasi MySQL/DB_HOST jika ada):

Cuplikan kode
DB_CONNECTION=sqlite
Setelah itu, jalankan migrasi database (jika ditanya create database, ketik yes):

Bash
php artisan migrate --seed
(Perintah --seed digunakan jika kamu sudah membuat data dummy/akun admin default)
php artisan migrate:fresh
php artisan db:seed --class=DummyDataSeeder
php artisan shield:generate --all (Ketik 0 lalu Enter, terus ketik no lalu Enter)
php artisan shield:super-admin (Ketik 0 lalu Enter, terus ketik 1 lalu Enter buat milih si "Admin Utama")

4. Install Dependencies Frontend (CSS/JS)
Install NPM dan build aset tampilannya agar rapi:
Bash
npm install
npm run build

5. Hubungkan Folder Upload (Storage Link)
Agar gambar profil atau file yang diupload bisa ditampilkan di web:
Bash
php artisan storage:link

6. Jalankan Aplikasi!
Nyalakan server lokal:
Bash
php artisan serve

Aplikasi SIAKAD sekarang bisa diakses melalui browser di alamat: http://localhost:8000 atau http://127.0.0.1:8000.

Untuk login admin/guru: Akses http://localhost:8000/admin

Untuk halaman utama/PPDB: Akses http://localhost:8000/
