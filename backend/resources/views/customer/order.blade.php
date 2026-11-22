@extends('customer.layout')

@section('title', 'Form Pemesanan Laundry')

@section('content')
<div x-data="customerOrderForm()" class="max-w-3xl mx-auto space-y-6">

    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-bold text-brand-600 uppercase tracking-wider bg-brand-50 px-2.5 py-0.5 rounded-full border border-brand-200">
                Pemesanan Online
            </span>
            <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Formulir Pesanan Laundry</h1>
            <p class="text-xs text-slate-500 mt-0.5">Isi detail cucian Anda, kurir kami akan segera menjemput ke alamat Anda.</p>
        </div>
        <div class="hidden sm:block">
            <span class="material-symbols-outlined text-4xl text-brand-600 bg-brand-50 p-3 rounded-2xl">
                receipt_long
            </span>
        </div>
    </div>

    <!-- Order Form -->
    <form action="{{ route('customer.order.post') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        @csrf

        <!-- 1. Data Pemesan -->
        <div>
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center">
                <span class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs mr-2">1</span>
                Data Pelanggan & Alamat Penjemputan
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                    <input type="text" name="customer_name" value="{{ $customer->name ?? 'Budi Santoso' }}" required
                           class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp / HP *</label>
                    <input type="text" name="customer_phone" value="{{ $customer->phone ?? '085711223344' }}" required
                           class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap Penjemputan *</label>
                <textarea name="pickup_address" rows="2" required placeholder="Contoh: Jl. Sudirman No. 45, RT 02/05, Menteng (Samping Minimarket)"
                          class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">{{ $customer->addresses->first()->full_address ?? 'Jl. Sudirman No. 45, Menteng, Jakarta Selatan' }}</textarea>
            </div>
        </div>

        <hr class="border-slate-100">

        <!-- 2. Pilihan Layanan -->
        <div>
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center">
                <span class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs mr-2">2</span>
                Pilihan Layanan Laundry
            </h2>
            
            <div class="space-y-3">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Paket Layanan *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($services as $svc)
                        <label class="p-3.5 rounded-xl border cursor-pointer transition flex items-start space-x-3"
                               :class="selectedServiceId == {{ $svc->id }} ? 'border-brand-600 bg-brand-50/50 ring-2 ring-brand-500/20' : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="service_id" value="{{ $svc->id }}" 
                                   @change="selectedServiceId = {{ $svc->id }}; pricePerUnit = {{ $svc->price_per_unit }}; serviceUnit = '{{ $svc->unit }}'"
                                   :checked="selectedServiceId == {{ $svc->id }}" class="mt-1 text-brand-600 focus:ring-brand-500">
                            <div class="flex-1">
                                <p class="text-xs font-bold text-slate-900">{{ $svc->name }}</p>
                                <p class="text-xs font-extrabold text-brand-700 mt-0.5">
                                    Rp {{ number_format($svc->price_per_unit, 0, ',', '.') }} / {{ $svc->unit }}
                                </p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Est. {{ $svc->estimated_hours }} Jam</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Metode Pengiriman & Bobot -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Antar-Jemput *</label>
                    <select name="delivery_type" x-model="deliveryType" class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                        <option value="pickup_delivery">Antar-Jemput Kurir (+Rp 10.000)</option>
                        <option value="dropoff_pickup">Drop-off ke Outlet Sendiri (Gratis)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Perkiraan Bobot / Jumlah Satuan *</label>
                    <div class="relative">
                        <input type="number" step="0.5" name="estimated_weight" x-model="estimatedWeight" required
                               class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400" x-text="serviceUnit"></span>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Bobot riil akan ditimbang ulang petugas kasir di gerai.</p>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Khusus Pakaian (Opsional)</label>
                <input type="text" name="customer_notes" placeholder="Contoh: Pisahkan pakaian putih, gunakan pewangi sakura ekstra..."
                       class="w-full text-sm rounded-xl border border-slate-200 px-3.5 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <hr class="border-slate-100">

        <!-- 3. Metode Pembayaran -->
        <div>
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center">
                <span class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs mr-2">3</span>
                Metode Pembayaran
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <label class="p-3.5 rounded-xl border cursor-pointer transition flex items-center space-x-2.5"
                       :class="paymentMethod == 'qris' ? 'border-brand-600 bg-brand-50/50' : 'border-slate-200'">
                    <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="text-brand-600">
                    <span class="text-xs font-bold text-slate-800">QRIS (GoPay/BCA)</span>
                </label>
                <label class="p-3.5 rounded-xl border cursor-pointer transition flex items-center space-x-2.5"
                       :class="paymentMethod == 'bank_transfer' ? 'border-brand-600 bg-brand-50/50' : 'border-slate-200'">
                    <input type="radio" name="payment_method" value="bank_transfer" x-model="paymentMethod" class="text-brand-600">
                    <span class="text-xs font-bold text-slate-800">Virtual Account Bank</span>
                </label>
                <label class="p-3.5 rounded-xl border cursor-pointer transition flex items-center space-x-2.5"
                       :class="paymentMethod == 'cash' ? 'border-brand-600 bg-brand-50/50' : 'border-slate-200'">
                    <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="text-brand-600">
                    <span class="text-xs font-bold text-slate-800">Tunai / COD</span>
                </label>
            </div>
        </div>

        <!-- 4. Rincian Estimasi Biaya -->
        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 space-y-2.5">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ringkasan Estimasi Biaya</h3>
            <div class="flex justify-between text-xs text-slate-600">
                <span>Subtotal Cucian (<span x-text="estimatedWeight"></span> <span x-text="serviceUnit"></span> x Rp <span x-text="pricePerUnit.toLocaleString('id-ID')"></span>)</span>
                <span class="font-semibold text-slate-900" x-text="'Rp ' + (estimatedWeight * pricePerUnit).toLocaleString('id-ID')"></span>
            </div>
            <div class="flex justify-between text-xs text-slate-600">
                <span>Ongkos Kirim Antar-Jemput</span>
                <span class="font-semibold text-slate-900" x-text="deliveryType == 'pickup_delivery' ? 'Rp 10.000' : 'Gratis'"></span>
            </div>
            <div class="pt-2 border-t border-slate-200 flex justify-between items-center text-sm">
                <span class="font-bold text-slate-900">Total Estimasi</span>
                <span class="text-lg font-extrabold text-brand-700" x-text="'Rp ' + ((estimatedWeight * pricePerUnit) + (deliveryType == 'pickup_delivery' ? 10000 : 0)).toLocaleString('id-ID')"></span>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-sm font-bold shadow-md transition flex items-center justify-center">
            <span class="material-symbols-outlined text-base mr-2">check_circle</span>
            Konfirmasi & Buat Pesanan Sekarang
        </button>

    </form>

</div>
@endsection

@section('scripts')
<script>
    function customerOrderForm() {
        return {
            selectedServiceId: {{ $selectedServiceId ?? 1 }},
            pricePerUnit: {{ $services->firstWhere('id', $selectedServiceId)?->price_per_unit ?? 8000 }},
            serviceUnit: '{{ $services->firstWhere('id', $selectedServiceId)?->unit ?? "kg" }}',
            deliveryType: 'pickup_delivery',
            estimatedWeight: 4.0,
            paymentMethod: 'qris'
        }
    }
</script>
@endsection

