@extends('admin.layout')

@section('title', 'Dokumentasi REST API Laundry')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="text-xs bg-brand-50 text-brand-700 px-2.5 py-0.5 rounded-full font-bold border border-brand-200">RESTful API v1</span>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-2">Spesifikasi & Dokumentasi API LaundryHub</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Dokumentasi teknis endpoint REST API sesuai dengan spesifikasi pada Blueprint & Analisis Jurnal Sistem Informasi.
                </p>
            </div>
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 text-xs">
                <p class="font-bold text-slate-700">Base URL:</p>
                <code class="text-brand-700 font-mono text-xs">http://127.0.0.1:8000/api/v1</code>
            </div>
        </div>

        <!-- Pre-generated Tokens -->
        <div class="mt-6 pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-[10px] font-bold uppercase text-slate-500">Bearer Token Admin (Superadmin):</span>
                <div class="flex items-center justify-between mt-1">
                    <code class="text-xs text-slate-800 font-mono truncate max-w-xs">{{ $adminToken }}</code>
                    <button onclick="navigator.clipboard.writeText('{{ $adminToken }}'); alert('Token Admin disalin!');" class="text-xs text-brand-600 hover:text-brand-800 font-semibold shrink-0 ml-2">
                        Salin
                    </button>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-[10px] font-bold uppercase text-slate-500">Bearer Token Pelanggan (Budi Santoso):</span>
                <div class="flex items-center justify-between mt-1">
                    <code class="text-xs text-slate-800 font-mono truncate max-w-xs">{{ $customerToken }}</code>
                    <button onclick="navigator.clipboard.writeText('{{ $customerToken }}'); alert('Token Pelanggan disalin!');" class="text-xs text-brand-600 hover:text-brand-800 font-semibold shrink-0 ml-2">
                        Salin
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- API Groups -->
    <div class="space-y-4">

        <!-- GROUP 1: AUTHENTICATION -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm flex items-center">
                    <span class="material-symbols-outlined text-base mr-2 text-brand-600">lock</span>
                    1. Modul Otentikasi & Pengguna (Auth Module)
                </h3>
                <span class="text-xs text-slate-500">Public & Sanctum Bearer</span>
            </div>
            <div class="divide-y divide-slate-100 p-4 space-y-4">
                
                <!-- POST /api/v1/auth/register -->
                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-xs font-bold font-mono">POST</span>
                        <code class="text-xs font-bold text-slate-900">/api/v1/auth/register</code>
                        <span class="text-xs text-slate-500">- Pendaftaran akun baru (Customer)</span>
                    </div>
                    <div class="bg-slate-900 text-slate-200 rounded-xl p-3 text-xs font-mono overflow-x-auto">
                        <span class="text-slate-400">// Body (JSON)</span><br>
                        { "name": "Budi Santoso", "email": "budi@gmail.com", "phone": "085711223344", "password": "password" }
                    </div>
                </div>

                <!-- POST /api/v1/auth/login -->
                <div class="space-y-2 pt-4">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-xs font-bold font-mono">POST</span>
                        <code class="text-xs font-bold text-slate-900">/api/v1/auth/login</code>
                        <span class="text-xs text-slate-500">- Login & generate Bearer Token</span>
                    </div>
                    <div class="bg-slate-900 text-slate-200 rounded-xl p-3 text-xs font-mono overflow-x-auto">
                        <span class="text-slate-400">// Body (JSON)</span><br>
                        { "email": "budi@gmail.com", "password": "password", "fcm_token": "optional_fcm_token" }
                    </div>
                </div>

                <!-- GET /api/v1/auth/me -->
                <div class="space-y-2 pt-4">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded bg-sky-100 text-sky-800 text-xs font-bold font-mono">GET</span>
                        <code class="text-xs font-bold text-slate-900">/api/v1/auth/me</code>
                        <span class="text-xs text-slate-500">- Profil pengguna & daftar alamat tersimpan (Bearer Token)</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- GROUP 2: ORDERS & REAL-TIME MONITORING -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm flex items-center">
                    <span class="material-symbols-outlined text-base mr-2 text-indigo-600">local_shipping</span>
                    2. Modul Pemesanan & Monitoring Real-time (Order & Tracking)
                </h3>
                <span class="text-xs text-slate-500">Core Feature</span>
            </div>
            <div class="divide-y divide-slate-100 p-4 space-y-4">

                <!-- GET /api/v1/orders -->
                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded bg-sky-100 text-sky-800 text-xs font-bold font-mono">GET</span>
                        <code class="text-xs font-bold text-slate-900">/api/v1/orders</code>
                        <span class="text-xs text-slate-500">- Daftar riwayat pesanan</span>
                    </div>
                </div>

                <!-- POST /api/v1/orders -->
                <div class="space-y-2 pt-4">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-xs font-bold font-mono">POST</span>
                        <code class="text-xs font-bold text-slate-900">/api/v1/orders</code>
                        <span class="text-xs text-slate-500">- Membuat pesanan laundry baru</span>
                    </div>
                    <div class="bg-slate-900 text-slate-200 rounded-xl p-3 text-xs font-mono overflow-x-auto">
                        <span class="text-slate-400">// Body (JSON)</span><br>
                        { "service_id": 1, "delivery_type": "pickup_delivery", "estimated_weight": 4.5, "customer_notes": "Tolong pisahkan pakaian putih" }
                    </div>
                </div>

                <!-- GET /api/v1/orders/{id}/track -->
                <div class="space-y-2 pt-4">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded bg-sky-100 text-sky-800 text-xs font-bold font-mono">GET</span>
                        <code class="text-xs font-bold text-slate-900">/api/v1/orders/{id}/track</code>
                        <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200">Tracking Stepper Endpoint</span>
                    </div>
                    <p class="text-xs text-slate-500">
                        Mengembalikan objek data lengkap untuk komponen <em>Stepper Widget</em> di aplikasi Flutter, termasuk nomor tahapan aktif (1-9), milestone yang sudah selesai, kurir bertugas, dan timestamp setiap proses.
                    </p>
                </div>

                <!-- PATCH /api/v1/orders/{id}/status -->
                <div class="space-y-2 pt-4">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-xs font-bold font-mono">PATCH</span>
                        <code class="text-xs font-bold text-slate-900">/api/v1/orders/{id}/status</code>
                        <span class="text-xs text-slate-500">- Update status pengerjaan (Kasir / Admin / Kurir)</span>
                    </div>
                    <div class="bg-slate-900 text-slate-200 rounded-xl p-3 text-xs font-mono overflow-x-auto">
                        <span class="text-slate-400">// Body (JSON)</span><br>
                        { "status": "washing", "notes": "Pakaian masuk mesin cuci kapasitas 10kg", "total_weight": 5.2 }
                    </div>
                </div>

            </div>
        </div>

        <!-- GROUP 3: SERVICES & PAYMENTS -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-sm flex items-center">
                    <span class="material-symbols-outlined text-base mr-2 text-emerald-600">payments</span>
                    3. Modul Layanan & Pembayaran (Services & Payments)
                </h3>
                <span class="text-xs text-slate-500">Katalog & Webhook</span>
            </div>
            <div class="divide-y divide-slate-100 p-4 space-y-4">
                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded bg-sky-100 text-sky-800 text-xs font-bold font-mono">GET</span>
                        <code class="text-xs font-bold text-slate-900">/api/v1/services</code>
                        <span class="text-xs text-slate-500">- Daftar paket layanan & harga per kg/pcs (Public)</span>
                    </div>
                </div>
                <div class="space-y-1 pt-4">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-xs font-bold font-mono">POST</span>
                        <code class="text-xs font-bold text-slate-900">/api/v1/payments/pay</code>
                        <span class="text-xs text-slate-500">- Inisiasi pembayaran QRIS / Virtual Account</span>
                    </div>
                </div>
                <div class="space-y-1 pt-4">
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-xs font-bold font-mono">POST</span>
                        <code class="text-xs font-bold text-slate-900">/api/v1/payments/callback</code>
                        <span class="text-xs text-slate-500">- Webhook Payment Gateway (Midtrans/Xendit)</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

