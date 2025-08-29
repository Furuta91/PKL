
---

# 📌 Project PKL

Proyek ini menggunakan **Laravel 12** dengan **Filament 3** sebagai admin panel.

---

## 🚀 Instalasi

1. **Clone repository**

   ```bash
   git clone https://github.com/Furuta91/PKL.git
   ```

2. **Install dependency**

   ```bash
   composer install
   ```

3. **Copy file `.env` dan konfigurasi database**

   ```bash
   cp .env.example .env
   ```

4. **Generate app key**

   ```bash
   php artisan key:generate
   ```

5. **Jalankan migrasi dan seeder**

   ```bash
   php artisan migrate --seed
   ```

   Seeder akan otomatis membuat user default berikut:

   ```
   Email    : admin@example.com
   Password : admin123
   ```

---

## 🔑 Login Admin

Setelah menjalankan seeder, buka URL berikut:

👉 [http://localhost/admin](http://localhost/admin)

Login dengan akun default di atas.

---

## 📚 Fitur Saat Ini

* Manajemen **Pengabdian**
* Manajemen **Penelitian**

---

## 🛠 Tech Stack

* Laravel 12
* Filament 3
* PHP 8.2+
* MySQL / MariaDB

---
