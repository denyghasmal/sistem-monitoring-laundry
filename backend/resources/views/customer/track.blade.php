@extends('customer.layout')

@section('title', 'Lacak Status Cucian')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Header & Search Box -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-5">
            <div>
                <span class="text-xs font-bold text-brand-600 uppercase tracking-wider bg-brand-50 px-2.5 py-0.5 rounded-full border border-brand-200">
                    Live Tracking
                </span>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Lacak Status Cucian Real-Time</h1>
                <p class="text-xs text-slate-500 mt-0.5">Pantau setiap tahapan pemrosesan cucian Anda dari penjemputan hingga selesai diantar.</p>
            </div>
            
            <a href="{{ route('customer.order') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                <span class="material-symbols-outlined text-sm mr-1.5">add_shopping_cart</span>
                Buat Pesanan Baru
            </a>
        </div>

        <!-- Search Form -->
        <form action="{{ route('customer.track') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="flex-1 w-full relative">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" name="code" value="{{ request('code', $order?->order_code) }}" placeholder="Ketik kode pesanan (misal: LND-20261103-W01)..." 
                       class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500 text-sm">
            </div>
            <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold transition flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-base mr-1.5">search</span>
                Cari Pesanan
            </button>
        </form>

        <!-- Quick Switcher to Other Recent Orders -->
        @if(isset($allOrders) && $allOrders->count() > 1)
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2 overflow-x-auto text-xs pb-1">
                <span class="text-slate-400 font-semibold shrink-0">Pilih Pesanan:</span>
                @foreach($allOrders as $o)
                    <a href="{{ route('customer.track', ['code' => $o->order_code]) }}" 
                       class="px-2.5 py-1 rounded-lg border font-mono transition shrink-0 {{ ($order && $order->id === $o->id) ? 'bg-brand-600 text-white border-brand-600 font-bold' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                        {{ $o->order_code }} ({{ $o->status_label }})
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    @if(!$order)
        <!-- Empty State -->
        <div class="bg-white p-12 rounded-2xl border border-slate-200 shadow-sm text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-400">
                <span class="material-symbols-outlined text-3xl">receipt_long</span>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Pesanan Tidak Ditemukan</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">Pastikan kode transaksi yang Anda masukkan benar, atau buat pesanan laundry baru sekarang.</p>
            <a href="{{ route('customer.order') }}" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition">
                Pesan Laundry Sekarang
            </a>
        </div>
    @else
        <!-- Order Overview Card -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 gap-4">
                <div>
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="font-mono text-xl font-extrabold text-slate-900 tracking-tight">{{ $order->order_code }}</span>
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-bold
                            @if(in_array($order->status, ['completed'])) bg-emerald-100 text-emerald-800 border border-emerald-200
                            @elseif(in_array($order->status, ['cancelled'])) bg-rose-100 text-rose-800 border border-rose-200
                            @elseif(in_array($order->status, ['washing', 'ironing', 'packing'])) bg-indigo-100 text-indigo-800 border border-indigo-200
                            @elseif(in_array($order->status, ['picking_up', 'delivering'])) bg-amber-100 text-amber-800 border border-amber-200
                            @else bg-sky-100 text-sky-800 border border-sky-200
                            @endif">
                            {{ $order->status_label }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">
                        Waktu Pesan: {{ $order->created_at->format('d M Y, H:i') }} WIB &bull; 
                        Layanan: <strong class="text-slate-800">{{ $order->service->name }}</strong>
                    </p>
                </div>

                <div class="flex items-center space-x-2">
                    @if($order->courier)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->courier->phone) }}?text=Halo%20Kurir%20{{ urlencode($order->courier->name) }},%20saya%20pelanggan%20pesanan%20{{ $order->order_code }}" 
                           target="_blank"
                           class="px-3.5 py-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 text-xs font-bold transition flex items-center">
                            <span class="material-symbols-outlined text-sm mr-1.5">chat</span>
                            WhatsApp Kurir ({{ $order->courier->name }})
                        </a>
                    @endif
                    <a href="{{ route('admin.dashboard') }}" 
                       title="Ubah status pesanan ini di Admin Dashboard untuk menguji live-tracking"
                       class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition flex items-center">
                        <span class="material-symbols-outlined text-sm mr-1">tune</span>
                        Simulasi Status (Admin)
                    </a>
                </div>
            </div>

            <!-- 9-Stage Visual Stepper -->
            <div class="py-8">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center">
                    <span class="material-symbols-outlined text-sm mr-1.5 text-brand-600">route</span>
                    Progres 9 Tahapan Layanan
                </h3>

                @php
                    $stages = [
                        ['key' => 'pending', 'label' => 'Pesanan Dibuat', 'icon' => 'receipt_long'],
                        ['key' => 'picking_up', 'label' => 'Penjemputan', 'icon' => 'local_shipping'],
                        ['key' => 'weighing', 'label' => 'Penimbangan', 'icon' => 'scale'],
                        ['key' => 'washing', 'label' => 'Pencucian', 'icon' => 'local_laundry_service'],
                        ['key' => 'drying', 'label' => 'Pengeringan', 'icon' => 'air'],
                        ['key' => 'ironing', 'label' => 'Penyetrikaan', 'icon' => 'iron'],
                        ['key' => 'packing', 'label' => 'Pengemasan', 'icon' => 'inventory_2'],
                        ['key' => 'delivering', 'label' => 'Pengantaran', 'icon' => 'delivery_dining'],
                        ['key' => 'completed', 'label' => 'Selesai', 'icon' => 'verified'],
                    ];

                    $statusKeys = array_column($stages, 'key');
                    $currentIndex = array_search($order->status, $statusKeys);
                    if ($currentIndex === false) $currentIndex = 0;
                @endphp

                <!-- Horizontal Stepper (Desktop) -->
                <div class="hidden md:grid grid-cols-9 gap-1 relative">
                    @foreach($stages as $idx => $stg)
                        @php
                            $isPassed = $idx < $currentIndex;
                            $isCurrent = $idx === $currentIndex;
                            $isUpcoming = $idx > $currentIndex;
                        @endphp
                        <div class="flex flex-col items-center text-center relative group">
                            <!-- Step Circle -->
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs transition z-10 
                                {{ $isCurrent ? 'bg-brand-600 text-white ring-4 ring-brand-100 shadow-md shadow-brand-500/30 animate-pulse' : '' }}
                                {{ $isPassed ? 'bg-emerald-600 text-white' : '' }}
                                {{ $isUpcoming ? 'bg-slate-100 text-slate-400 border border-slate-200' : '' }}">
                                @if($isPassed)
                                    <span class="material-symbols-outlined text-lg">check</span>
                                @else
                                    <span class="material-symbols-outlined text-base">{{ $stg['icon'] }}</span>
                                @endif
                            </div>
                            
                            <!-- Label -->
                            <span class="text-[11px] font-semibold mt-2 leading-tight {{ $isCurrent ? 'text-brand-700 font-bold' : ($isPassed ? 'text-slate-800' : 'text-slate-400') }}">
                                {{ $stg['label'] }}
                            </span>

                            @if($idx < 8)
                                <!-- Connecting Bar -->
                                <div class="absolute top-5 left-1/2 w-full h-1 -z-0 {{ $idx < $currentIndex ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Vertical Stepper (Mobile) -->
                <div class="md:hidden space-y-4">
                    @foreach($stages as $idx => $stg)
                        @php
                            $isPassed = $idx < $currentIndex;
                            $isCurrent = $idx === $currentIndex;
                            $isUpcoming = $idx > $currentIndex;
                        @endphp
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs shrink-0 
                                {{ $isCurrent ? 'bg-brand-600 text-white ring-2 ring-brand-200 animate-pulse' : '' }}
                                {{ $isPassed ? 'bg-emerald-600 text-white' : '' }}
                                {{ $isUpcoming ? 'bg-slate-100 text-slate-400 border border-slate-200' : '' }}">
                                @if($isPassed)
                                    <span class="material-symbols-outlined text-sm">check</span>
                                @else
                                    <span class="material-symbols-outlined text-sm">{{ $stg['icon'] }}</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <span class="text-xs font-semibold {{ $isCurrent ? 'text-brand-700 font-bold' : ($isPassed ? 'text-slate-800' : 'text-slate-400') }}">
                                    {{ $stg['label'] }}
                                </span>
                                @if($isCurrent)
                                    <span class="text-[10px] bg-brand-50 text-brand-700 px-1.5 py-0.5 rounded ml-1 font-bold">Sedang Berlangsung</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Details Grid: Laundry Spec, Billing & Courier -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-6 border-t border-slate-100">
                <!-- Laundry Spec -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Spesifikasi Cucian</span>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Tipe Antar-Jemput:</span>
                        <span class="font-bold text-slate-800 capitalize">{{ str_replace('_', ' ', $order->delivery_type) }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Berat Taksiran:</span>
                        <span class="font-bold text-slate-800">{{ $order->total_weight }} {{ $order->service->unit }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Tarif / Satuan:</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($order->price_per_unit, 0, ',', '.') }}</span>
                    </div>
                    @if($order->customer_notes)
                        <div class="pt-2 border-t border-slate-200 text-xs">
                            <span class="text-slate-500 block mb-0.5">Catatan Khusus:</span>
                            <span class="italic text-slate-700">"{{ $order->customer_notes }}"</span>
                        </div>
                    @endif
                </div>

                <!-- Billing & Payment -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Rincian Pembayaran</span>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Subtotal Cucian:</span>
                        <span class="text-slate-800">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Ongkir Antar-Jemput:</span>
                        <span class="text-slate-800">Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs font-bold pt-2 border-t border-slate-200 text-slate-900">
                        <span>Total Tagihan:</span>
                        <span class="text-brand-700 font-extrabold text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs pt-1">
                        <span class="text-slate-500">Status Pembayaran:</span>
                        <span class="font-bold px-2 py-0.5 rounded-full text-[10px] {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ strtoupper($order->payment_status) }} ({{ strtoupper($order->payment_method) }})
                        </span>
                    </div>
                </div>

                <!-- Courier & Address -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Kurir & Alamat</span>
                    <div class="text-xs">
                        <span class="text-slate-500 block">Kurir Bertugas:</span>
                        @if($order->courier)
                            <span class="font-bold text-slate-900 flex items-center mt-0.5">
                                <span class="material-symbols-outlined text-sm mr-1 text-brand-600">person</span>
                                {{ $order->courier->name }} ({{ $order->courier->phone }})
                            </span>
                        @else
                            <span class="text-amber-700 font-semibold italic mt-0.5 block">Menunggu penugasan kurir</span>
                        @endif
                    </div>
                    <div class="text-xs pt-2 border-t border-slate-200">
                        <span class="text-slate-500 block">Alamat Penjemputan:</span>
                        <span class="text-slate-800 font-medium block mt-0.5 leading-relaxed">{{ $order->address->full_address ?? 'Alamat Pemesan' }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Logs History Timeline -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center">
                    <span class="material-symbols-outlined text-sm mr-1.5 text-brand-600">history</span>
                    Riwayat Log Aktivitas Cucian
                </h3>

                <div class="space-y-3">
                    @forelse($order->statusLogs as $log)
                        <div class="flex items-start space-x-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-7 h-7 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-sm">update</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold text-slate-900">{{ $log->title }}</h4>
                                    <span class="text-[10px] text-slate-400">{{ $log->created_at->format('d M H:i') }} WIB</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-0.5">{{ $log->notes }}</p>
                                @if($log->actor)
                                    <span class="text-[10px] text-slate-400 mt-1 inline-block">Diperbarui oleh: <strong>{{ $log->actor->name }}</strong></span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada catatan log aktivitas.</p>
                    @endforelse
                </div>
            </div>

        </div>
    @endif

</div>
@endsection

