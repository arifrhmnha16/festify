# FESTIFY - Technical Prompt Laravel 12

## Role
Kamu adalah fullstack Laravel developer. Bangun aplikasi web bernama **Festify**, yaitu sistem e-ticket konser berbasis Laravel 12.

Gunakan bahasa Indonesia untuk nama menu, label, dan teks UI. Jangan terlalu banyak basa-basi. Langsung implementasi fitur.

## Tech Stack
- Laravel 12
- Blade Template
- Tailwind CSS
- Alpine.js jika dibutuhkan
- MySQL
- Laravel Auth custom multi-role
- QR Code package untuk generate dan scan QR
- File upload untuk poster konser dan bukti pembayaran jika pembayaran manual

## Style UI
Buat tampilan website dengan gaya modern, clean, dan premium seperti website ticketing/concert event.

Referensi style: Artatix-style ticketing website.

Aturan desain:
- Jangan gunakan neon.
- Jangan gunakan gradient mencolok.
- Jangan gunakan warna terlalu rame.
- Gunakan warna dasar putih, hitam, abu-abu, dan aksen merah/oranye/kuning yang soft.
- Banyak whitespace.
- Card event rapi.
- Typography besar dan tegas.
- Navbar clean.
- Button rounded tapi tetap profesional.
- Landing page harus terlihat seperti website penjualan tiket konser profesional.
- Responsive untuk desktop dan mobile.

## Gambaran Sistem
Festify adalah sistem informasi e-ticket konser berbasis web.

Alur utama:
1. User register/login.
2. User melihat daftar konser.
3. User memilih konser.
4. User membuat pemesanan tiket.
5. User melakukan pembayaran.
6. Jika pembayaran berhasil, sistem menerbitkan E-Ticket berisi kode tiket dan QR Code.
7. Saat hari konser, user menunjukkan E-Ticket ke petugas loket.
8. Petugas loket scan E-Ticket.
9. Jika valid, E-Ticket ditukar menjadi gelang.
10. Gelang memiliki QR Code.
11. Petugas gate scan gelang.
12. Jika gelang valid dan belum digunakan, user boleh masuk ke area konser.

## Aktor Sistem

### 1. User
User adalah pembeli tiket konser.

Fitur user:
- Register
- Login
- Logout
- Melihat landing page
- Melihat daftar konser
- Search konser
- Filter konser berdasarkan tanggal/lokasi jika memungkinkan
- Melihat detail konser
- Membuat pemesanan tiket
- Melakukan pembayaran
- Melihat status pembayaran
- Melihat E-Ticket
- Download / print E-Ticket
- Melihat riwayat pemesanan
- Melihat status E-Ticket
- Melihat status gelang jika sudah ditukar

### 2. Admin
Admin bertugas mengelola seluruh data sistem.

PENTING:
Di ERD sebelumnya admin hanya terlihat mengelola konser, tetapi pada implementasi sistem admin harus bisa mengelola semuanya.

Fitur admin:
- Login admin
- Dashboard admin
- Kelola konser
- Kelola user
- Kelola petugas
- Kelola pemesanan
- Kelola pembayaran
- Kelola E-Ticket
- Kelola gelang
- Kelola riwayat scan
- Lihat laporan
- Validasi pembayaran jika pembayaran manual
- Monitoring jumlah tiket terjual
- Monitoring jumlah E-Ticket terbit
- Monitoring jumlah E-Ticket sudah ditukar
- Monitoring jumlah gelang aktif
- Monitoring jumlah user yang sudah masuk gate

### 3. Petugas Loket
Petugas loket bertugas menukar E-Ticket menjadi gelang.

Fitur petugas loket:
- Login petugas loket
- Dashboard loket
- Scan QR Code E-Ticket
- Validasi E-Ticket
- Cek status pembayaran
- Cek apakah E-Ticket sudah ditukar atau belum
- Cek apakah E-Ticket sesuai konser
- Update status E-Ticket menjadi sudah ditukar
- Generate / aktifkan gelang
- Menampilkan data gelang aktif
- Menolak E-Ticket tidak valid

### 4. Petugas Gate
Petugas gate bertugas memvalidasi gelang saat user masuk area konser.

Fitur petugas gate:
- Login petugas gate
- Dashboard gate
- Scan QR Code gelang
- Validasi gelang
- Cek apakah gelang aktif
- Cek apakah gelang belum digunakan
- Update status gelang menjadi sudah masuk
- Menampilkan akses diterima
- Menampilkan akses ditolak jika gelang tidak valid

## Halaman Public / Landing Page
Buat landing page Festify dengan tampilan seperti website ticketing modern.

Section landing page:
1. Navbar
   - Logo Festify
   - Beranda
   - Konser
   - Cara Kerja
   - Login
   - Cari Tiket

2. Hero Section
   - Headline besar: **Temukan Konser Favoritmu dan Masuk Pakai E-Ticket**
   - Subheadline: **Festify memudahkan pembelian tiket konser online, penerbitan E-Ticket, penukaran gelang, dan validasi masuk venue.**
   - CTA:
     - Lihat Konser
     - Cara Kerja

3. Search Event Section
   - Input cari konser/artis
   - Filter lokasi
   - Filter tanggal
   - Tombol cari

4. Featured Concerts
   - Card konser
   - Poster konser
   - Nama konser
   - Artist
   - Tanggal
   - Jam
   - Venue
   - Harga
   - Stok
   - Tombol detail / beli tiket

5. Cara Kerja
   - Login / Register
   - Pilih Konser
   - Pesan Tiket
   - Bayar
   - Terima E-Ticket
   - Tukar Gelang
   - Scan Gate

6. E-Ticket Preview
   - Tampilan contoh tiket
   - QR Code dummy
   - Kode tiket
   - Status tiket

7. Footer
   - Tentang Festify
   - Kontak
   - Link sosial media dummy

## Halaman User

### User Dashboard
Isi:
- Sapaan user
- Pesanan aktif
- E-Ticket aktif
- Status pembayaran terbaru
- Riwayat pemesanan terbaru
- Konser rekomendasi

### Daftar Konser
Isi:
- Search konser
- Filter tanggal
- Filter lokasi
- Card konser
- Button detail

### Detail Konser
Isi:
- Poster konser
- Nama konser
- Artist
- Tanggal
- Jam
- Venue
- Deskripsi
- Harga
- Stok
- Seat / Zone jika ada
- Tombol beli tiket

### Checkout / Pemesanan
Isi:
- Detail konser
- Input jumlah tiket
- Pilihan zone jika digunakan
- Ringkasan harga
- Total bayar
- Tombol lanjut pembayaran

### Pembayaran
Isi:
- Kode pemesanan
- Nama konser
- Total bayar
- Metode pembayaran
- Upload bukti bayar jika manual
- Status pembayaran
- Tombol bayar / submit

### E-Ticket
Isi:
- Nama konser
- Nama user
- Kode E-Ticket
- QR Code E-Ticket
- Tanggal konser
- Venue
- Status E-Ticket
- Instruksi: **Tukarkan E-Ticket ini di loket untuk mendapatkan gelang.**
- Tombol download / print

### Riwayat Pemesanan
Isi:
- Kode pemesanan
- Nama konser
- Tanggal pemesanan
- Total bayar
- Status pemesanan
- Status pembayaran
- Status E-Ticket
- Tombol lihat detail

## Halaman Admin

### Dashboard Admin
Card statistik:
- Total konser
- Total user
- Total pemesanan
- Total pembayaran berhasil
- Total E-Ticket terbit
- Total gelang aktif
- Total user masuk gate

### Kelola Konser
CRUD:
- Tambah konser
- Edit konser
- Hapus konser
- Upload poster
- Atur nama konser
- Artis
- Venue/lokasi
- Tanggal
- Jam
- Harga
- Stok
- Status konser

### Kelola User
- List user
- Detail user
- Edit user
- Hapus user jika perlu

### Kelola Petugas
- Tambah petugas
- Edit petugas
- Hapus petugas
- Role petugas: loket / gate

### Kelola Pemesanan
- List pemesanan
- Detail pemesanan
- Status pemesanan
- Filter berdasarkan status

### Kelola Pembayaran
- List pembayaran
- Detail pembayaran
- Validasi pembayaran manual
- Update status bayar menjadi berhasil/gagal

### Kelola E-Ticket
- List E-Ticket
- Detail E-Ticket
- Status belum ditukar / sudah ditukar / invalid
- Lihat QR Code

### Kelola Gelang
- List gelang
- Detail gelang
- Status aktif / sudah masuk / invalid
- Lihat QR Code gelang

### Laporan
- Laporan pemesanan
- Laporan pembayaran
- Laporan E-Ticket
- Laporan gelang
- Laporan riwayat scan

## Halaman Petugas Loket
Buat halaman khusus petugas loket.

Fitur:
- Login petugas loket
- Scan QR Code E-Ticket
- Input manual kode E-Ticket jika scan gagal
- Tampilkan data E-Ticket
- Validasi:
  - E-Ticket ditemukan
  - Pembayaran berhasil
  - E-Ticket belum ditukar
  - Konser sesuai hari ini
- Jika valid:
  - Update status E-Ticket menjadi sudah_ditukar
  - Generate atau aktifkan gelang
  - Tampilkan QR Code gelang
  - Simpan riwayat scan
- Jika tidak valid:
  - Tampilkan pesan penukaran ditolak

## Halaman Petugas Gate
Buat halaman khusus petugas gate.

Fitur:
- Login petugas gate
- Scan QR Code gelang
- Input manual kode gelang jika scan gagal
- Tampilkan data gelang
- Validasi:
  - Gelang ditemukan
  - Gelang aktif
  - Gelang belum digunakan
- Jika valid:
  - Update status gelang menjadi sudah_masuk
  - Simpan riwayat scan
  - Tampilkan akses diterima
- Jika tidak valid:
  - Tampilkan akses ditolak

## Database Tables

### users
- id
- name
- email
- password
- phone
- created_at
- updated_at

### admins
- id
- name
- username
- password
- created_at
- updated_at

### officers / petugas
- id
- name
- username
- password
- role enum: loket, gate
- created_at
- updated_at

### concerts
- id
- admin_id
- name
- artist
- description
- venue
- date
- time
- poster
- price
- stock
- seat_zone
- status enum: aktif, selesai, dibatalkan
- created_at
- updated_at

### orders / pemesanan
- id
- user_id
- concert_id
- order_code
- order_date
- ticket_quantity
- total_price
- order_status enum: pending, paid, cancelled, expired
- created_at
- updated_at

### payments
- id
- order_id
- payment_method
- total_amount
- payment_status enum: pending, success, failed
- payment_date
- payment_proof
- created_at
- updated_at

### e_tickets
- id
- order_id
- user_id
- concert_id
- ticket_code
- ticket_qr_code
- ticket_status enum: belum_ditukar, sudah_ditukar, invalid
- issued_at
- exchanged_at
- created_at
- updated_at

### wristbands / gelang
- id
- e_ticket_id
- concert_id
- wristband_code
- wristband_qr_code
- wristband_status enum: aktif, sudah_masuk, invalid
- activated_at
- entered_at
- created_at
- updated_at

### scan_histories / riwayat_scan
- id
- officer_id
- e_ticket_id nullable
- wristband_id nullable
- scan_type enum: scan_eticket, scan_gelang
- scan_result enum: berhasil, gagal
- message
- scanned_at
- created_at
- updated_at

## Relasi
- Admin has many Concerts
- User has many Orders
- Concert has many Orders
- Order belongs to User
- Order belongs to Concert
- Order has one Payment
- Order has many E-Tickets
- E-Ticket belongs to Order
- E-Ticket belongs to User
- E-Ticket belongs to Concert
- E-Ticket has one Wristband
- Wristband belongs to E-Ticket
- Wristband belongs to Concert
- Officer has many ScanHistories

Catatan:
Walaupun pada ERD admin hanya terlihat kelola konser, implementasi admin tetap harus bisa kelola semua data utama: konser, user, petugas, pemesanan, pembayaran, E-Ticket, gelang, dan laporan.

## Status Rules

### Order Status
- pending: pemesanan dibuat tapi belum dibayar
- paid: pembayaran berhasil
- cancelled: pemesanan dibatalkan
- expired: pemesanan melewati batas waktu

### Payment Status
- pending: pembayaran belum divalidasi
- success: pembayaran berhasil
- failed: pembayaran gagal

### E-Ticket Status
- belum_ditukar: E-Ticket aktif dan belum ditukar gelang
- sudah_ditukar: E-Ticket sudah ditukar menjadi gelang
- invalid: E-Ticket tidak valid

### Wristband Status
- aktif: gelang aktif dan bisa digunakan masuk
- sudah_masuk: gelang sudah digunakan masuk gate
- invalid: gelang tidak valid

## QR Code Rules
- QR Code E-Ticket berisi ticket_code.
- QR Code Gelang berisi wristband_code.
- E-Ticket hanya bisa ditukar satu kali.
- Gelang hanya bisa digunakan masuk satu kali.
- Jika QR Code sudah digunakan, sistem harus menolak penggunaan ulang.
- Setiap scan harus masuk ke scan_histories.

## Routing

### Public
- /
- /concerts
- /concerts/{id}
- /login
- /register

### User
- /user/dashboard
- /user/concerts
- /user/orders
- /user/orders/{id}
- /user/payments/{id}
- /user/e-tickets
- /user/e-tickets/{id}

### Admin
- /admin/dashboard
- /admin/concerts
- /admin/users
- /admin/officers
- /admin/orders
- /admin/payments
- /admin/e-tickets
- /admin/wristbands
- /admin/reports

### Petugas Loket
- /loket/dashboard
- /loket/scan-eticket
- /loket/exchange/{ticket_code}

### Petugas Gate
- /gate/dashboard
- /gate/scan-wristband
- /gate/validate/{wristband_code}

## UI Pages to Build First
Prioritaskan halaman berikut:
1. Landing page
2. Login / register
3. User daftar konser
4. User detail konser
5. User checkout
6. User payment
7. User E-Ticket
8. Admin dashboard
9. Admin kelola konser
10. Petugas loket scan E-Ticket
11. Petugas gate scan gelang

## Seeder
Buat data dummy:
- 1 admin
- 2 petugas loket
- 2 petugas gate
- 5 user
- 6 konser
- beberapa order
- beberapa payment
- beberapa e-ticket
- beberapa gelang

## Security
- Password harus di-hash.
- Gunakan middleware auth.
- Gunakan middleware role.
- User tidak boleh akses halaman admin.
- Petugas loket tidak boleh akses halaman gate.
- Petugas gate tidak boleh akses halaman loket.
- QR Code bekas tidak boleh digunakan ulang.

## Validation
Tambahkan validasi:
- Register user
- Login
- Tambah konser
- Update konser
- Buat pemesanan
- Upload bukti pembayaran
- Validasi pembayaran
- Scan E-Ticket
- Scan gelang

## Expected Output
Hasil akhir harus berupa aplikasi Laravel 12 yang bisa dijalankan dengan:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

Aplikasi harus memiliki:
- Landing page clean ala website ticketing
- Auth multi-role
- Dashboard user
- Dashboard admin
- Dashboard petugas loket
- Dashboard petugas gate
- CRUD konser
- Pemesanan tiket
- Pembayaran
- Generate E-Ticket QR Code
- Scan E-Ticket untuk tukar gelang
- Scan gelang untuk masuk konser
- Laporan admin

## Important Notes
- Jangan membuat UI neon.
- Jangan membuat gradient mencolok.
- Jangan membuat desain terlalu ramai.
- Fokus desain seperti website ticketing profesional.
- Gunakan copywriting singkat dan jelas.
- Jangan terlalu banyak komentar tidak perlu.
- Jangan menjelaskan terlalu panjang di output.
- Langsung implementasi kode.
