@extends('customer.layout')

@section('title', 'Layanan Laundry & Antar Jemput')

@section('content')
<div class="space-y-10">

    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-r from-brand-700 via-brand-600 to-sky-500 rounded-3xl p-8 sm:p-12 text-white shadow-lg">
        <div class="relative z-10 max-w-2xl space-y-4">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur">
                <span class="material-symbols-outlined text-sm mr-1.5">local_shipping</span> Antar-Jemput Cepat & Higienis
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                Cuci Pakaian Tanpa Repot, Pantau Progresnya Secara Real-Time!
            </h1>
            <p class="text-sm sm:text-base text-sky-100 leading-relaxed">
                Pesan penjemputan pakaian dari rumah atau kantor. Pantau setiap tahapan pencucian mulai dari penimbangan, pencucian mesin, pengeringan, hingga pengantaran kurir langsung ke depan pintu Anda.
            </p>
            <div class="pt-2 flex flex-wrap gap-3">
                <a href="{{ route('customer.order') }}" class="px-6 py-3 bg-white text-brand-700 hover:bg-sky-50 rounded-xl font-bold text-sm shadow-md transition flex items-center">
                    <span class="material-symbols-outlined text-base mr-2">add_shopping_cart</span>
                    Pesan Laundry Sekarang
                </a>
                <a href="{{ route('customer.track') }}" class="px-5 py-3 bg-brand-800/60 hover:bg-brand-800 text-white rounded-xl font-bold text-sm border border-white/20 transition flex items-center">
                    <span class="material-symbols-outlined text-base mr-2">timeline</span>
                    Lacak Status Cucian
                </a>
            </div>
        </div>
        <!-- Decorative Icon -->
        <span class="material-symbols-outlined absolute -right-6 -bottom-10 text-[260px] text-white/10 select-none pointer-events-none">
            local_laundry_service
        </span>
    </div>

    <!-- Quick Tracking Search Bar -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('customer.track') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="flex-1 w-full relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" name="code" placeholder="Punya kode transaksi? Masukkan kode (contoh: LND-20261103-W01)..." 
                       class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 text-sm">
            </div>
            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-base mr-1.5">timeline</span>
                Lacak Cucian
            </button>
        </form>
    </div>

    <!-- Services Catalog Grid -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Pilihan Paket & Layanan Laundry</h2>
                <p class="text-xs text-slate-500 mt-0.5">Deterjen antibakteri, setrika uap halus, dan parfum harum pilihan.</p>
            </div>
            <a href="{{ route('customer.order') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($services as $svc)
                <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between hover:shadow-md hover:border-brand-300 transition group">
                    <div>
                        <div class="flex items-start justify-between">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-brand-50 text-brand-700 border border-brand-200">
                                {{ $svc->category }}
                            </span>
                            <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center group-hover:bg-brand-600 group-hover:text-white transition">
                                <span class="material-symbols-outlined text-xl">{{ $svc->icon ?? 'local_laundry_service' }}</span>
                            </div>
                        </div>

                        <h3 class="font-bold text-slate-900 text-base mt-3">{{ $svc->name }}</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">{{ $svc->description }}</p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-[11px] text-slate-400">Tarif / Satuan</p>
                            <p class="text-lg font-extrabold text-brand-700">
                                Rp {{ number_format($svc->price_per_unit, 0, ',', '.') }}
                                <span class="text-xs font-medium text-slate-500">/ {{ $svc->unit }}</span>
                            </p>
                            <p class="text-[10px] text-slate-400">Est. {{ $svc->estimated_hours }} Jam</p>
                        </div>
                        <a href="{{ route('customer.order', ['service_id' => $svc->id]) }}" 
                           class="px-4 py-2 bg-brand-50 hover:bg-brand-600 text-brand-700 hover:text-white rounded-xl text-xs font-bold transition flex items-center">
                            Pilih Paket
                            <span class="material-symbols-outlined text-sm ml-1">arrow_forward</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 9-Step Lifecycle Explainer -->
    <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
        <div class="max-w-xl mx-auto text-center space-y-2 mb-8">
            <span class="text-xs font-bold text-brand-600 uppercase tracking-wider bg-brand-50 px-3 py-1 rounded-full border border-brand-200">Transparansi Layanan</span>
            <h2 class="text-xl font-bold text-slate-900">Alur 9 Tahapan Pengerjaan Cucian Anda</h2>
            <p class="text-xs text-slate-500">Setiap proses tercatat di sistem secara otomatis dan dapat Anda pantau detik demi detik.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-3 text-center">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs mx-auto flex items-center justify-center">1</div>
                <p class="text-xs font-bold text-slate-800 mt-2">Pemesanan</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Order dibuat di sistem</p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs mx-auto flex items-center justify-center">2</div>
                <p class="text-xs font-bold text-slate-800 mt-2">Penjemputan</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Kurir jemput pakaian</p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs mx-auto flex items-center justify-center">3</div>
                <p class="text-xs font-bold text-slate-800 mt-2">Penimbangan</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Penetapan bobot timbangan</p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs mx-auto flex items-center justify-center">4</div>
                <p class="text-xs font-bold text-slate-800 mt-2">Pencucian</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Dicuci mesin higienis</p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs mx-auto flex items-center justify-center">5</div>
                <p class="text-xs font-bold text-slate-800 mt-2">Pengeringan</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Mesin dryer steril</p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs mx-auto flex items-center justify-center">6</div>
                <p class="text-xs font-bold text-slate-800 mt-2">Penyetrikaan</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Setrika uap licin</p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs mx-auto flex items-center justify-center">7</div>
                <p class="text-xs font-bold text-slate-800 mt-2">Siap Kirim</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Packing higienis</p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <div class="w-7 h-7 rounded-full bg-brand-600 text-white font-bold text-xs mx-auto flex items-center justify-center">8</div>
                <p class="text-xs font-bold text-slate-800 mt-2">Pengantaran</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Kurir otw ke alamat</p>
            </div>
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                <div class="w-7 h-7 rounded-full bg-emerald-600 text-white font-bold text-xs mx-auto flex items-center justify-center">9</div>
                <p class="text-xs font-bold text-emerald-800 mt-2">Selesai</p>
                <p class="text-[11px] text-emerald-600 mt-0.5">Diterima pelanggan</p>
            </div>
        </div>
    </div>

</div>
@endsection

