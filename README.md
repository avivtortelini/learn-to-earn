# Sistem Informasi Manajemen Kamar Kost

Aplikasi web Laravel untuk mengelola kamar kost, pelanggan, tagihan bulanan, bukti pembayaran, pengeluaran, laporan keuangan, dan akses user receptionist/pemilik.

## Fitur

- Login internal untuk `owner` dan `receptionist`.
- Dashboard ringkasan kamar, tagihan, pemasukan, dan pengeluaran.
- CRUD kamar, pelanggan, tagihan, pembayaran, pengeluaran, dan user.
- Generate tagihan bulanan untuk seluruh penghuni aktif.
- Upload bukti bayar gambar/PDF ke Laravel storage.
- Verifikasi pembayaran dan laporan keuangan khusus pemilik.

## Setup

Pastikan PHP 8.2+, Composer, dan MySQL/MariaDB tersedia di PATH.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
php artisan serve
```

Atur koneksi database di `.env` sebelum menjalankan migration.

## Akun Seeder

- Pemilik: `pemilik@kost.local` / `password`
- Receptionist: `receptionist@kost.local` / `password`
