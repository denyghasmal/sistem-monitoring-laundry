# 🧺 Rancang Bangun Sistem Informasi Pemesanan & Monitoring Layanan Laundry Berbasis Mobile & Web

Sistem informasi terpadu pemesanan dan pelacakan cucian secara real-time yang dikembangkan berdasarkan **Blueprint Rancang Bangun** dan **Analisis Jurnal Aplikasi Laundry**.

Aplikasi ini mencakup:
1. **Web Portal Pelanggan (Full-Width Responsive)**: Untuk konsumen memesan cucian, cek ongkir, dan melacak status pengerjaan secara langsung dari browser laptop/HP.
2. **Dashboard Operasional Gerai & Kasir**: Untuk admin mengelola pesanan, menimbang cucian, menugaskan kurir, dan memperbarui tahapan cuci.
3. **Klien Mobile Native (Flutter)**: Arsitektur *Clean Feature-First* siap kompilasi ke Android (`.apk`) / iOS.
4. **Backend REST API (Laravel 11)**: 34 endpoint API aman dengan autentikasi Bearer Token & Role-Based Access Control.

---

## 🏗️ Struktur Repositori

```
laundry/
├── backend/                  # REST API & Web Applications (Laravel 11)
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   ├── Api/          # Auth, Order, Service, Courier, Payment, Admin Controllers
│   │   │   └── Web/          # CustomerWebController & DashboardController
│   │   ├── Http/Middleware/  # ApiAuth (Bearer Token), RoleMiddleware
│   │   └── Models/           # User, Order, Service, Address, OrderStatusLog, Review
│   ├── database/
│   │   ├── migrations/       # 9 Migrasi skema database relasional
│   │   └── seeders/          # Seeder data pengguna, alamat, layanan & riwayat tracking
│   ├── resources/views/
│   │   ├── customer/         # Web Portal Pelanggan (Beranda, Pesan, Lacak 9-Step, Riwayat)
│   │   └── admin/            # Dashboard Admin, Simulator Mobile, API Explorer
│   ├── routes/
│   │   ├── api.php           # 34 endpoint REST API versi 1
│   │   └── web.php           # Rute Web Portal Pelanggan & Dashboard Admin
│   └── tests/Feature/        # 9 Automated Feature Tests (109 assertions, 100% Pass)
│
├── mobile_app/               # Aplikasi Klien Mobile Native (Flutter / Dart)
│   ├── lib/
│   │   ├── core/             # Network (Dio Client), App Theme, Constants, Utils
│   │   ├── features/
│   │   │   ├── auth/         # Login, Register, Profile & AuthProvider
│   │   │   ├── home/         # Beranda, Katalog Layanan, Banner Promo
│   │   │   ├── order/        # Formulir Pemesanan & Riwayat Transaksi
│   │   │   ├── tracking/     # Stepper Timeline Monitoring Real-time
│   │   │   └── notification/ # Firebase Cloud Messaging Handler
│   │   └── main.dart
│   └── pubspec.yaml
│
├── .gitignore                # Filter file Git (vendor, .env, build, cache)
├── Analisis Jurnal Aplikasi Laundry.docx
└── Blueprint Rancang Bangun Aplikasi Pemesanan dan Monitoring Layanan Laundry.docx
```

---

## ⚡ Panduan Instalasi & Menjalankan Proyek

### 1. Kebutuhan Sistem (Prerequisites)
- PHP >= 8.2 (disarankan PHP 8.2 - 8.5)
- Composer >= 2.0
- Web Browser modern (Chrome, Edge, Firefox, Safari)
- *(Opsional untuk mobile build)*: Flutter SDK >= 3.0.0 & Android Studio

---

### 2. Menjalankan Backend & Web Portal (Laravel)

Buka terminal di dalam folder proyek:
```bash
# Masuk ke direktori backend
cd backend

# Salin file konfigurasi environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Jalankan migrasi database dan pengisian data demo (seed)
php artisan migrate --seed

# Jalankan development server
php artisan serve
```

Server akan aktif di `http://127.0.0.1:8000`.

---

### 3. Akses Demo Online via Cloudflare Tunnel (Tanpa Install bagi Dosen/Penguji)

Agar aplikasi dapat diakses langsung oleh penguji atau dari smartphone di luar jaringan lokal tanpa perlu konfigurasi port forwarding:
```bash
# Jalankan Cloudflare Tunnel di terminal terpisah
cloudflared tunnel --url http://127.0.0.1:8000
```
Sistem akan menghasilkan link publik HTTPS (misal: `https://[nama-acak].trycloudflare.com`) yang dapat langsung dibuka dari perangkat manapun di internet.

---

## 🌐 Daftar Halaman Aplikasi

| Antarmuka | URL Akses | Deskripsi & Fitur |
| :--- | :--- | :--- |
| **Web Portal Pelanggan** | [http://127.0.0.1:8000/portal](http://127.0.0.1:8000/portal) | Katalog layanan laundry, estimasi biaya, dan pelacakan cepat. |
| **Pemesanan Online** | [http://127.0.0.1:8000/portal/pesan](http://127.0.0.1:8000/portal/pesan) | Formulir order cucian, hitung total otomatis, opsi antar-jemput. |
| **Lacak Status Real-Time** | [http://127.0.0.1:8000/portal/lacak](http://127.0.0.1:8000/portal/lacak) | Visual Stepper 9 tahapan pengerjaan cucian & kontak WhatsApp kurir. |
| **Riwayat Transaksi** | [http://127.0.0.1:8000/portal/riwayat](http://127.0.0.1:8000/portal/riwayat) | Histori transaksi, rincian biaya, dan status pembayaran. |
| **Dashboard Admin Gerai** | [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin) | Monitoring pesanan, update status cuci, dan penugasan kurir. |
| **Simulator Layar HP** | [http://127.0.0.1:8000/simulator](http://127.0.0.1:8000/simulator) | Mockup preview interaktif aplikasi mobile Flutter. |
| **Interactive API Explorer** | [http://127.0.0.1:8000/api-docs](http://127.0.0.1:8000/api-docs) | Dokumentasi lengkap 34 endpoint REST API. |

---

## 🔑 Kredensial Akun Demo (Default Seeders)

| Role / Peran | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin Gerai** | `admin@laundry.com` | `password` | Akses penuh dashboard operasional kasir & admin |
| **Kurir 1** | `kurir@laundry.com` | `password` | Kurir Bambang Pamungkas (Antar-Jemput Reguler) |
| **Kurir 2** | `kurir2@laundry.com` | `password` | Kurir Rian Hidayat (Kurir Express) |
| **Pelanggan 1** | `budi@gmail.com` | `password` | Budi Santoso (Menteng, Jakarta Pusat) |
| **Pelanggan 2** | `siti@gmail.com` | `password` | Siti Rahmawati (Apartemen Kalibata) |

---

## 🔄 Alur Siklus Pesanan (9-Stage Lifecycle Stepper)

Setiap pesanan dipantau melalui 9 tahapan pengerjaan yang tercatat di tabel `order_status_logs`:
```
[1. Pending] ──> [2. Penjemputan] ──> [3. Penimbangan] ──> [4. Pencucian] ──> [5. Pengeringan]
                                                                                   │
[9. Selesai] <── [8. Pengantaran] <── [7. Pengemasan] <── [6. Penyetrikaan] <──────┘
```

---

## 🧪 Pengujian Otomatis (Automated Testing)

Untuk memverifikasi keandalan API dan alur web, jalankan perintah pengujian:
```bash
cd backend
php artisan test
```
**Hasil:** `9 passed (109 assertions)` menguji autentikasi token, CRUD pesanan, transisi status 9-tahap, kalkulasi tarif, hak akses peran (RBAC), dan pemesanan via Web Portal.
