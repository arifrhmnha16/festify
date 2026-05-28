# Festify

Festify adalah aplikasi web e-ticket konser berbasis Laravel 12. Aplikasi ini mendukung pembelian tiket, penerbitan E-Ticket QR Code, penukaran E-Ticket menjadi gelang, dan validasi gelang di gate venue.

## Fitur

- Landing page ticketing konser
- Auth multi-role: user, admin, petugas loket, petugas gate
- User dapat memilih konser, memilih area venue, checkout, bayar instan, dan langsung menerima E-Ticket
- Admin panel dengan sidebar dan CRUD data utama
- Petugas loket scan QR E-Ticket atau input kode manual untuk cetak gelang
- Petugas gate scan QR gelang atau input kode manual untuk validasi masuk
- QR Code E-Ticket dan gelang
- Mobile bottom navigation untuk user

## Tech Stack

- Laravel 12
- Blade
- Tailwind CSS
- SQLite default lokal, bisa diganti ke MySQL
- Simple QR Code

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
npm run dev
php artisan serve
```

Untuk build production:

```bash
npm run build
```

## Akun Demo

Admin:

```text
username: admin
password: password
```

User:

```text
email: nadia@example.com
password: password
```

Petugas Loket:

```text
username: loket1
password: password
```

Petugas Gate:

```text
username: gate1
password: password
```

## Route Utama

- `/` landing page
- `/login` login user
- `/admin/login` login admin
- `/loket/login` login petugas loket
- `/gate/login` login petugas gate
- `/user/e-tickets` tiket user
- `/admin/dashboard` dashboard admin
- `/loket/scan-eticket` scan E-Ticket
- `/gate/scan-wristband` scan gelang

## Catatan

- Pembayaran user saat ini otomatis sukses dan langsung menerbitkan E-Ticket.
- Upload poster konser disimpan di `storage/app/public/posters`.
- Scanner QR kamera memakai browser `BarcodeDetector`, paling aman di Chrome/Edge modern.
