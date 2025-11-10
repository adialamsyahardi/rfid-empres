# RFID e-Presensi & E-Kantin (rfid-empres)

Laravel 10 application for RFID-based attendance and e-canteen management.
Implements school attendance, sholat attendance (based on daily jadwal), custom activity
attendance, and e-canteen balance/topup/payment — all using RFID cards. Includes admin
management for users, jadwal sholat, and presensi settings.

## Tech Stack

- **Backend**: PHP 8.1+, Laravel 10
- **Auth**: Session auth, middleware `auth` and `admin`
- **Frontend**: Vite 5, Bootstrap 5, Axios
- **Database**: MySQL

## MVP Features

- **Presensi Sekolah**
  - Scan RFID for `masuk` / `keluar` with late/early logic using `PengaturanWaktu`.
  - Update keterangan: `hadir|izin|sakit|tanpa_keterangan`.
  - View today’s presensi and list of users who haven’t checked in.

- **Presensi Sholat**
  - Scan RFID for `subuh|dzuhur|ashar|maghrib|isya` using daily `JadwalSholat`.
  - Late calculation with statuses: terlalu awal, tepat waktu, terlambat.
  - Manual entry for keterangan.
  - Export CSV per tanggal.
  - JSON endpoints for today’s jadwal and latest presensi.

- **Presensi Kustom**
  - Admin can manage custom schedules.
  - Scan RFID per schedule; auto status (tepat waktu/terlambat/terlalu awal) and minutes late.
  - AJAX: latest presensi, stats, and list of users who haven’t presensi for a schedule.

- **E-Kantin**
  - Cek saldo (by RFID) and show limit status.
  - Top up saldo (by RFID) and record transaksi.
  - Pembayaran (by RFID) with per-user daily limit enforcement.
  - Riwayat transaksi and toggle limit on/off per user.

- **Jadwal Sholat Management**
  - Sync monthly jadwal from API `api.myquran.com`.
  - Bulk override a waktu sholat for a month.
  - Edit one day’s times, delete a day. Tracks manual edits.

- **Pengaturan Waktu**
  - Manage presensi windows and tolerances for `sekolah|sholat|kustom`.

## Getting Started

### Prerequisites

- PHP 8.1+
- Composer
- MySQL
- Node.js 18+ (Vite 5)

### Setup

1. Copy env and set database config
   ```bash
   cp .env.example .env
   ```
2. Install PHP dependencies
   ```bash
   composer install
   php artisan key:generate
   ```
3. Migrate database (seed optional but provides default users)
   ```bash
   php artisan migrate
   php artisan db:seed # optional
   ```
4. Install frontend deps and build assets
   ```bash
   npm ci # or: npm install
   npm run dev
   ```
5. Run the app
   ```bash
   php artisan serve
   ```

### Seeded Accounts (if you run seeder)

- Admin: `admin@rfid.com` / `admin123`
- Sample users: see `database/seeders/DatabaseSeeder.php`

## License

This software licensed under the [MIT license](https://opensource.org/licenses/MIT).