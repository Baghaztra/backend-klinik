# Clinic Project Backend

## 📌 Deskripsi

**Clinic Project Backend** adalah backend API untuk aplikasi mobile **Clinic Project**, dibangun menggunakan **Laravel**. Backend ini menyediakan layanan REST API untuk aplikasi Flutter, termasuk fitur autentikasi berbasis **Laravel Sanctum** dan manajemen data klinik.

Selain itu, backend ini juga dilengkapi dengan **Filament Admin Panel** untuk kemudahan administrasi data pasien, dokter, dan riwayat pemeriksaan melalui antarmuka web.

---

## 📦 Teknologi yang Digunakan

* **Laravel 11**
* **Laravel Sanctum** (Autentikasi API)
* **Filament Admin Panel**
* **MySQL** (Database)

---

## 📜 Fitur Utama

* 🔐 Autentikasi login/register via API untuk aplikasi Flutter
* 📄 Manajemen data pasien melalui API dan admin panel
* 👨‍⚕️ Manajemen data dokter melalui API dan admin panel
* 📋 Pencatatan riwayat pemeriksaan pasien
* 📊 Dashboard admin berbasis Filament

---

## 🛠️ Cara Menjalankan Proyek

1. Clone repository ini:

   ```bash
   git clone https://github.com/Baghaztra/backend-klinik.git
   ```
2. Masuk ke folder proyek:

   ```bash
   cd clinic_backend
   ```
3. Install dependency Laravel:

   ```bash
   composer install
   ```
4. Salin file `.env.example` menjadi `.env` lalu atur konfigurasi database.
5. Generate application key:

   ```bash
   php artisan key:generate
   ```
6. Jalankan migrasi database:

   ```bash
   php artisan migrate --seed
   ```
7. Jalankan server Laravel:

   ```bash
   php artisan serve
   ```

8. Login
   - Username: `admin@example.com`
   - Password: `1`
---

## 📄 Lisensi

Backend ini dikembangkan untuk keperluan akademik semester 5 di Politeknik Negeri Padang.

> **Baghaztra Van Ril** (Developer)
