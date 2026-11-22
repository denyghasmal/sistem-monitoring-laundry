# LaundryHub Mobile - Client Mobile App (Flutter)

Aplikasi mobile pemesanan dan monitoring layanan laundry berbasis Flutter (Dart) yang terhubung dengan backend REST API Laravel.

## 📱 Arsitektur Kode (Feature-First Architecture)

Sesuai dengan Bagian 7 pada dokumen Blueprint:
```
lib/
├── core/
│   ├── constants/       # AppColors, ApiEndpoints
│   ├── network/         # ApiClient (Dio Wrapper & Auth Interceptor)
│   ├── theme/           # AppTheme (Material 3 & Plus Jakarta Sans)
│   └── utils/           # Formatters (Rupiah & Tanggal)
├── features/
│   ├── auth/            # UserModel, AuthProvider, Login & Register Screen
│   ├── home/            # HomeScreen, Dashboard Layanan, Banner Promo
│   ├── order/           # OrderModel, ServiceModel, OrderCreate, OrderHistory
│   ├── tracking/        # Real-time Timeline Stepper Widget & TrackingScreen
│   └── notification/    # FcmService (Firebase Cloud Messaging Token Sync)
└── main.dart            # Entry Point & MultiProvider Setup
```

## 🚀 Fitur Utama
1. **Real-time Status Tracking Timeline (Stepper Widget)**:
   - Visualisasi progres pengerjaan cucian (1-9 tahapan):
     `Pemesanan` $\rightarrow$ `Penjemputan` $\rightarrow$ `Penimbangan & Tagihan` $\rightarrow$ `Pencucian` $\rightarrow$ `Pengeringan` $\rightarrow$ `Penyetrikaan` $\rightarrow$ `Siap Kirim` $\rightarrow$ `Dalam Pengantaran` $\rightarrow$ `Selesai`
   - Dilengkapi catatan proses pengerjaan aktual dari kasir/admin gerai.
   - Info kontak & nomor WhatsApp kurir penjemput/pengantar.
2. **Katalog & Pemesanan Layanan**:
   - Pemilihan kategori Kiloan, Satuan, Dry Clean, dan Express.
   - Pilihan metode antar-jemput kurir atau drop-off langsung ke outlet.
   - Kalkulasi otomatis perkiraan tarif dan ongkos kirim.
3. **Otentikasi & Keamanan**:
   - Autentikasi token Bearer tersimpan aman di SharedPreferences.
   - Sinkronisasi otomatis token FCM untuk push notification saat status berubah.

## 🛠️ Menjalankan Aplikasi Mobile
```bash
flutter pub get
flutter run
```
*Catatan: Pastikan server Laravel berjalan di `http://127.0.0.1:8000` (atau `http://10.0.2.2:8000` pada Android Emulator).*

