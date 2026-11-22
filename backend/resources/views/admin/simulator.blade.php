@extends('admin.layout')

@section('title', 'Simulator Aplikasi Mobile Pelanggan')

@section('content')
<div x-data="mobileSimulator()" class="max-w-5xl mx-auto space-y-6">

    <!-- Top explanation banner -->
    <div class="bg-gradient-to-r from-brand-700 to-sky-600 text-white rounded-2xl p-6 shadow-md flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-xs bg-white/20 text-white px-2.5 py-0.5 rounded-full font-semibold">Flutter Mobile Client Preview</span>
            <h1 class="text-xl font-bold mt-1.5">Simulator Antarmuka Aplikasi Mobile Laundry</h1>
            <p class="text-xs text-sky-100 mt-1 max-w-xl">
                Visualisasi interaktif aplikasi mobile Flutter client untuk Pelanggan. Menggunakan REST API backend Laravel yang sama dengan aplikasi Flutter asli.
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-semibold backdrop-blur transition flex items-center">
                <span class="material-symbols-outlined text-base mr-1">arrow_back</span>
                Kembali ke Dashboard Admin
            </a>
        </div>
    </div>

    <!-- Phone Simulator Container -->
    <div class="flex flex-col lg:flex-row items-center justify-center gap-8 py-4">

        <!-- Mobile Phone Mockup -->
        <div class="w-[390px] h-[780px] bg-slate-900 rounded-[50px] p-3.5 shadow-2xl ring-1 ring-slate-800 flex flex-col relative">
            
            <!-- Dynamic Island / Speaker Notch -->
            <div class="absolute top-6 left-1/2 -translate-x-1/2 w-28 h-5 bg-black rounded-full z-50 flex items-center justify-center">
                <div class="w-2.5 h-2.5 bg-slate-800 rounded-full mr-2"></div>
                <div class="w-1.5 h-1.5 bg-sky-900 rounded-full"></div>
            </div>

            <!-- Phone Screen Inner Frame -->
            <div class="w-full h-full bg-slate-50 rounded-[40px] overflow-hidden flex flex-col relative border border-slate-200">
                
                <!-- Status Bar -->
                <div class="h-10 bg-white/90 backdrop-blur px-6 flex items-center justify-between text-xs font-bold text-slate-800 pt-1 shrink-0 z-40 border-b border-slate-100">
                    <span>09:41</span>
                    <div class="flex items-center space-x-1.5 text-xs">
                        <span class="material-symbols-outlined text-sm">signal_cellular_alt</span>
                        <span class="material-symbols-outlined text-sm">wifi</span>
                        <span class="material-symbols-outlined text-sm">battery_full</span>
                    </div>
                </div>

                <!-- Screen Header -->
                <div class="bg-white px-5 py-3 border-b border-slate-100 flex items-center justify-between shrink-0 shadow-xs">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-full bg-brand-500 text-white font-bold flex items-center justify-center text-xs">
                            B
                        </div>
                        <div>
                            <p class="text-[11px] text-slate-400 leading-tight">Halo, Pelanggan</p>
                            <p class="text-xs font-bold text-slate-800">{{ $customer->name ?? 'Budi Santoso' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-slate-600">
                            <span class="material-symbols-outlined text-sm">notifications</span>
                        </button>
                    </div>
                </div>

                <!-- Scrollable Body Content -->
                <div class="flex-1 overflow-y-auto p-4 space-y-4">

                    <!-- VIEW 1: HOME / BERANDA -->
                    <div x-show="activeTab === 'home'" class="space-y-4">
                        <!-- Promo Banner Carousel -->
                        <div class="bg-gradient-to-r from-brand-600 to-sky-500 rounded-2xl p-4 text-white shadow-sm relative overflow-hidden">
                            <div class="relative z-10">
                                <span class="bg-white/25 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Diskon 25%</span>
                                <h3 class="font-extrabold text-sm mt-1.5">Cuci Kilat Express</h3>
                                <p class="text-[11px] text-sky-100 mt-0.5">Selesai dalam 6 jam, pakaian harum & rapi siap pakai!</p>
                                <button @click="activeTab = 'order'" class="mt-2.5 px-3 py-1 bg-white text-brand-700 rounded-lg text-xs font-bold shadow-xs">
                                    Pesan Sekarang
                                </button>
                            </div>
                            <span class="material-symbols-outlined absolute -right-2 -bottom-4 text-8xl text-white/10">
                                local_laundry_service
                            </span>
                        </div>

                        <!-- Active Order Shortcut Card -->
                        @if($orders->isNotEmpty())
                            @php $activeOrder = $orders->first(); @endphp
                            <div class="bg-white rounded-2xl p-3.5 border border-slate-200 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-brand-600 uppercase tracking-wide">Pesanan Terkini</span>
                                    <span class="text-[10px] bg-brand-50 text-brand-700 px-2 py-0.5 rounded-full font-semibold border border-brand-200">
                                        Step {{ $activeOrder->status_step }}/9
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center justify-between">
                                    <div>
                                        <p class="font-bold text-xs text-slate-900">{{ $activeOrder->service->name }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $activeOrder->order_code }}</p>
                                    </div>
                                    <button @click="loadTracking({{ $activeOrder->id }})" class="px-2.5 py-1 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-[11px] font-semibold flex items-center">
                                        <span class="material-symbols-outlined text-xs mr-1">timeline</span>
                                        Pantau
                                    </button>
                                </div>
                            </div>
                        @endif

                        <!-- Services Grid -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-xs font-bold text-slate-800">Layanan Laundry</h4>
                                <span class="text-[11px] text-brand-600 font-semibold cursor-pointer">Lihat Semua</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2.5">
                                @foreach($services as $svc)
                                    <div @click="selectServiceForOrder({{ json_encode($svc) }})"
                                         class="bg-white p-3 rounded-2xl border border-slate-200/80 hover:border-brand-500 hover:shadow-xs transition cursor-pointer flex flex-col justify-between">
                                        <div>
                                            <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center mb-2">
                                                <span class="material-symbols-outlined text-base">{{ $svc->icon ?? 'wash' }}</span>
                                            </div>
                                            <h5 class="font-bold text-xs text-slate-900 line-clamp-1">{{ $svc->name }}</h5>
                                            <p class="text-[10px] text-slate-400 mt-0.5 capitalize">{{ $svc->category }}</p>
                                        </div>
                                        <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                                            <span class="text-xs font-extrabold text-brand-700">Rp {{ number_format($svc->price_per_unit, 0, ',', '.') }}</span>
                                            <span class="text-[10px] text-slate-400">/{{ $svc->unit }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- VIEW 2: TRACKING / REAL-TIME MONITORING (The Stepper Widget) -->
                    <div x-show="activeTab === 'tracking'" class="space-y-4">
                        <!-- Order Selector Dropdown -->
                        <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Pilih Pesanan untuk Dipantau:</label>
                            <select x-model="selectedTrackingId" @change="fetchTrackingData()" class="w-full text-xs font-bold border-slate-200 rounded-xl px-2.5 py-1.5 bg-slate-50">
                                @foreach($orders as $ord)
                                    <option value="{{ $ord->id }}">
                                        {{ $ord->order_code }} - {{ $ord->service->name }} ({{ $ord->status_label }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Stepper Status Header Card -->
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900" x-text="trackingData?.order_code"></span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200"
                                      x-text="trackingData?.status_label"></span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-500 pt-1 border-t border-slate-100">
                                <span>Tagihan: <strong class="text-slate-900" x-text="'Rp ' + (trackingData?.total_price || 0).toLocaleString('id-ID')"></strong></span>
                                <span class="capitalize" :class="trackingData?.payment_status === 'paid' ? 'text-emerald-600 font-bold' : 'text-amber-600 font-semibold'"
                                      x-text="trackingData?.payment_status === 'paid' ? 'Lunas' : 'Belum Bayar'"></span>
                            </div>
                        </div>

                        <!-- Stepper Timeline Visualization (Clean Architecture) -->
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                            <h4 class="text-xs font-bold text-slate-800 mb-3 flex items-center justify-between">
                                <span>Timeline Pengerjaan</span>
                                <button @click="fetchTrackingData()" class="text-[11px] text-brand-600 font-semibold flex items-center">
                                    <span class="material-symbols-outlined text-xs mr-0.5 animate-spin" x-show="isTrackingLoading">sync</span>
                                    Segarkan
                                </button>
                            </h4>

                            <!-- Timeline Nodes -->
                            <div class="space-y-3">
                                <template x-for="item in trackingData?.timeline" :key="item.step">
                                    <div class="flex items-start space-x-3">
                                        <!-- Step Indicator Bubble -->
                                        <div class="flex flex-col items-center">
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold transition"
                                                 :class="item.is_completed ? (item.is_current ? 'bg-brand-600 text-white ring-4 ring-brand-100' : 'bg-emerald-500 text-white') : 'bg-slate-200 text-slate-400'">
                                                <span class="material-symbols-outlined text-xs" x-show="item.is_completed && !item.is_current">check</span>
                                                <span x-show="!item.is_completed || item.is_current" x-text="item.step"></span>
                                            </div>
                                            <div x-show="item.step < (trackingData?.timeline?.length || 9)" 
                                                 class="w-0.5 h-6 mt-1"
                                                 :class="item.is_completed && !item.is_current ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                                        </div>
                                        <!-- Step Text -->
                                        <div class="flex-1 pb-1">
                                            <div class="flex items-center justify-between">
                                                <p class="text-xs font-bold" 
                                                   :class="item.is_current ? 'text-brand-700' : (item.is_completed ? 'text-slate-800' : 'text-slate-400')" 
                                                   x-text="item.title"></p>
                                                <span class="text-[9px] text-slate-400" x-text="item.timestamp || ''"></span>
                                            </div>
                                            <p class="text-[10px] mt-0.5" 
                                               :class="item.is_current ? 'text-slate-600 font-medium' : 'text-slate-400'" 
                                               x-text="item.description"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Courier Contact Card if assigned -->
                        <template x-if="trackingData?.courier">
                            <div class="bg-purple-50 p-3.5 rounded-2xl border border-purple-200 flex items-center justify-between">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-9 h-9 rounded-full bg-purple-600 text-white font-bold flex items-center justify-center text-xs">
                                        <span class="material-symbols-outlined text-sm">directions_bike</span>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-purple-600 font-bold uppercase">Kurir Ditugaskan</p>
                                        <p class="text-xs font-bold text-purple-900" x-text="trackingData?.courier?.name"></p>
                                        <p class="text-[10px] text-purple-700" x-text="trackingData?.courier?.phone"></p>
                                    </div>
                                </div>
                                <a :href="'https://wa.me/' + trackingData?.courier?.phone" target="_blank" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-[10px] font-bold">
                                    Hubungi
                                </a>
                            </div>
                        </template>
                    </div>

                    <!-- VIEW 3: BUAT PESANAN BARU -->
                    <div x-show="activeTab === 'order'" class="space-y-4">
                        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs space-y-3">
                            <h4 class="text-xs font-bold text-slate-900">Form Pemesanan Laundry</h4>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Layanan Laundry</label>
                                <select x-model="orderForm.service_id" class="w-full text-xs border-slate-200 rounded-xl px-2.5 py-2 bg-slate-50">
                                    @foreach($services as $svc)
                                        <option value="{{ $svc->id }}">
                                            {{ $svc->name }} (Rp {{ number_format($svc->price_per_unit, 0, ',', '.') }}/{{ $svc->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Metode Pengiriman</label>
                                <select x-model="orderForm.delivery_type" class="w-full text-xs border-slate-200 rounded-xl px-2.5 py-2 bg-slate-50">
                                    <option value="pickup_delivery">Antar-Jemput Kurir (+Rp 10.000)</option>
                                    <option value="dropoff_pickup">Drop-off Sendiri ke Outlet (Gratis Ongkir)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Perkiraan Berat / Satuan</label>
                                <input type="number" step="0.5" x-model="orderForm.estimated_weight" placeholder="Contoh: 3.5 kg" class="w-full text-xs border-slate-200 rounded-xl px-2.5 py-2 bg-slate-50">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Catatan Pakaian</label>
                                <textarea rows="2" x-model="orderForm.customer_notes" placeholder="Contoh: Pisahkan pakaian luntur, perbanyak pewangi..." class="w-full text-xs border-slate-200 rounded-xl px-2.5 py-2 bg-slate-50"></textarea>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Metode Pembayaran</label>
                                <select x-model="orderForm.payment_method" class="w-full text-xs border-slate-200 rounded-xl px-2.5 py-2 bg-slate-50">
                                    <option value="qris">QRIS (GoPay, OVO, ShopeePay, BCA)</option>
                                    <option value="bank_transfer">Virtual Account Bank</option>
                                    <option value="cash">Tunai (Cash / COD)</option>
                                </select>
                            </div>

                            <button @click="submitOrder()" :disabled="isSubmitting" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center">
                                <span class="material-symbols-outlined text-xs mr-1" x-show="isSubmitting">sync</span>
                                Konfirmasi & Buat Pesanan
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Bottom Navigation Bar (Mobile Nav) -->
                <div class="h-16 bg-white border-t border-slate-200/80 px-6 flex items-center justify-around shrink-0 z-40">
                    <button @click="activeTab = 'home'" class="flex flex-col items-center justify-center transition"
                            :class="activeTab === 'home' ? 'text-brand-600 font-bold' : 'text-slate-400'">
                        <span class="material-symbols-outlined text-xl">home</span>
                        <span class="text-[10px] mt-0.5">Beranda</span>
                    </button>

                    <button @click="activeTab = 'order'" class="flex flex-col items-center justify-center transition"
                            :class="activeTab === 'order' ? 'text-brand-600 font-bold' : 'text-slate-400'">
                        <span class="material-symbols-outlined text-xl">add_circle</span>
                        <span class="text-[10px] mt-0.5">Pesan</span>
                    </button>

                    <button @click="activeTab = 'tracking'; fetchTrackingData()" class="flex flex-col items-center justify-center transition"
                            :class="activeTab === 'tracking' ? 'text-brand-600 font-bold' : 'text-slate-400'">
                        <span class="material-symbols-outlined text-xl">timeline</span>
                        <span class="text-[10px] mt-0.5">Lacak</span>
                    </button>
                </div>

                <!-- Home Indicator Bar -->
                <div class="h-4 bg-white flex items-center justify-center pb-1">
                    <div class="w-24 h-1 bg-slate-300 rounded-full"></div>
                </div>

            </div>
        </div>

        <!-- Sidebar Helper Guide & Actions -->
        <div class="max-w-md w-full space-y-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-900 text-sm flex items-center">
                    <span class="material-symbols-outlined text-brand-600 mr-2">info</span>
                    Tentang Simulator & Sinkronisasi
                </h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Simulator di sebelah kiri berinteraksi langsung dengan REST API Laravel (<code class="bg-slate-100 px-1 py-0.5 rounded text-brand-700">/api/v1</code>).
                </p>
                <div class="space-y-2 text-xs text-slate-600">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-start space-x-2">
                        <span class="material-symbols-outlined text-base text-brand-600 mt-0.5">touch_app</span>
                        <div>
                            <strong>Uji coba Real-time:</strong> Buka tab <em>Dashboard Admin</em> di jendela terpisah, lalu ubah status pesanan (misal dari <em>Washing</em> ke <em>Ironing</em>).
                        </div>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-start space-x-2">
                        <span class="material-symbols-outlined text-base text-emerald-600 mt-0.5">check_circle</span>
                        <div>
                            <strong>Timeline Stepper:</strong> Klik tombol <em>Segarkan</em> di Simulator untuk melihat tahapan stepper langsung bergerak maju secara otomatis!
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wide">Data Pelanggan Aktif</h4>
                <div class="flex items-center space-x-3 text-xs">
                    <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 font-bold flex items-center justify-center">
                        B
                    </div>
                    <div>
                        <p class="font-bold text-slate-800">{{ $customer->name ?? 'Budi Santoso' }}</p>
                        <p class="text-slate-500">{{ $customer->email ?? 'budi@gmail.com' }}</p>
                        <p class="text-slate-400 text-[11px]">{{ $customer->addresses->first()->full_address ?? 'Jakarta' }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    function mobileSimulator() {
        return {
            activeTab: 'home',
            selectedTrackingId: {{ $orders->first()?->id ?? 1 }},
            trackingData: null,
            isTrackingLoading: false,
            isSubmitting: false,
            orderForm: {
                service_id: {{ $services->first()?->id ?? 1 }},
                delivery_type: 'pickup_delivery',
                estimated_weight: 4.0,
                customer_notes: '',
                payment_method: 'qris'
            },

            init() {
                this.fetchTrackingData();
            },

            selectServiceForOrder(svc) {
                this.orderForm.service_id = svc.id;
                this.activeTab = 'order';
            },

            loadTracking(orderId) {
                this.selectedTrackingId = orderId;
                this.activeTab = 'tracking';
                this.fetchTrackingData();
            },

            async fetchTrackingData() {
                if (!this.selectedTrackingId) return;
                this.isTrackingLoading = true;
                try {
                    const res = await fetch(`/api/v1/orders/${this.selectedTrackingId}/track`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    const json = await res.json();
                    if (json.success) {
                        this.trackingData = json.data;
                    }
                } catch (e) {
                    console.error('Failed to load tracking data:', e);
                } finally {
                    this.isTrackingLoading = false;
                }
            },

            async submitOrder() {
                this.isSubmitting = true;
                try {
                    // Login token for Budi Santoso
                    const authRes = await fetch('/api/v1/auth/login', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({ email: 'budi@gmail.com', password: 'password' })
                    });
                    const authJson = await authRes.json();
                    const token = authJson.data.token;

                    const res = await fetch('/api/v1/orders', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + token
                        },
                        body: JSON.stringify(this.orderForm)
                    });

                    const json = await res.json();
                    if (json.success) {
                        alert('Pesanan berhasil dibuat: ' + json.data.order_code);
                        this.selectedTrackingId = json.data.id;
                        this.activeTab = 'tracking';
                        await this.fetchTrackingData();
                    } else {
                        alert('Gagal: ' + json.message);
                    }
                } catch (e) {
                    alert('Error creating order: ' + e);
                } finally {
                    this.isSubmitting = false;
                }
            }
        }
    }
</script>
@endsection

