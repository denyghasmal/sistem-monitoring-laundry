@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
<div x-data="dashboardData()" class="space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard Operasional Gerai</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau seluruh antrean cucian, penimbangan, penugasan kurir, dan pergerakan status secara real-time.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('mobile.simulator') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-brand-600 to-sky-500 hover:from-brand-700 hover:to-sky-600 text-white rounded-xl text-sm font-semibold shadow-sm transition">
                <span class="material-symbols-outlined text-lg mr-2">phone_iphone</span>
                Buka Simulator Mobile Pelanggan
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Revenue -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pendapatan</p>
                    <p class="text-2xl font-extrabold text-slate-900 mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">payments</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-500">
                <span class="text-emerald-600 font-semibold flex items-center mr-1">
                    <span class="material-symbols-outlined text-sm">trending_up</span>
                    Rp {{ number_format($stats['today_revenue'], 0, ',', '.') }}
                </span>
                <span>hari ini</span>
            </div>
        </div>

        <!-- Card 2: Total Orders -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pesanan</p>
                    <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $stats['total_orders'] }} Transaksi</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">receipt_long</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-500">
                <span class="text-amber-600 font-semibold mr-1">{{ $stats['pending_orders'] }} Menunggu</span>
                <span>konfirmasi & jemput</span>
            </div>
        </div>

        <!-- Card 3: In Process -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sedang Diproses</p>
                    <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ $stats['in_process'] }} Pakaian</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">local_laundry_service</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-500">
                <span>Cuci, Pengeringan & Penyetrikaan</span>
            </div>
        </div>

        <!-- Card 4: Delivering / Ready -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Siap / Dalam Antar</p>
                    <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['ready_orders'] + $stats['delivering'] }} Paket</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">two_wheeler</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-500">
                <span class="font-semibold text-slate-700 mr-1">{{ $stats['total_couriers'] }} Kurir Aktif</span>
                <span>di lapangan</span>
            </div>
        </div>
    </div>

    <!-- Orders Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Antrean Pesanan & Pelacakan Status</h2>
                <p class="text-xs text-slate-500">Ubah tahapan pengerjaan laundry pelanggan secara bertahap sesuai lifecycle.</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg font-medium">
                    Total: {{ $orders->count() }} Pesanan
                </span>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/75 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Kode & Pelanggan</th>
                        <th class="px-6 py-4">Layanan</th>
                        <th class="px-6 py-4">Tahapan Lifecycle</th>
                        <th class="px-6 py-4">Bobot / Tarif</th>
                        <th class="px-6 py-4">Kurir</th>
                        <th class="px-6 py-4">Pembayaran</th>
                        <th class="px-6 py-4 text-right">Aksi Operasional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/50 transition">
                            <!-- Code & Customer -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 flex items-center space-x-1.5">
                                    <span>{{ $order->order_code }}</span>
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ $order->customer->name ?? 'Guest' }} • {{ $order->customer->phone ?? '-' }}
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $order->created_at->format('d M Y H:i') }}
                                </div>
                            </td>

                            <!-- Service -->
                            <td class="px-6 py-4">
                                <span class="font-medium text-slate-800">{{ $order->service->name }}</span>
                                <div class="text-xs text-slate-400 mt-0.5">
                                    Kategori: <span class="capitalize">{{ $order->service->category }}</span>
                                </div>
                            </td>

                            <!-- Status Lifecycle Stepper Badge -->
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'pending'    => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'picked_up'  => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'weighing'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'washing'    => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                        'drying'     => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'ironing'    => 'bg-teal-50 text-teal-700 border-teal-200',
                                        'ready'      => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'delivering' => 'bg-sky-50 text-sky-700 border-sky-200',
                                        'completed'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'cancelled'  => 'bg-rose-50 text-rose-700 border-rose-200',
                                    ];
                                    $badgeClass = $statusClasses[$order->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                @endphp
                                <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                    <span class="mr-1.5 font-bold">Step {{ $order->status_step }}/9:</span>
                                    <span>{{ $order->status_label }}</span>
                                </div>
                                @if($order->statusLogs->isNotEmpty())
                                    <p class="text-[11px] text-slate-500 mt-1 max-w-[200px] truncate" title="{{ $order->statusLogs->last()->notes }}">
                                        "{{ $order->statusLogs->last()->notes }}"
                                    </p>
                                @endif
                            </td>

                            <!-- Weight / Price -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">
                                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    @if($order->total_weight)
                                        {{ $order->total_weight }} kg
                                    @elseif($order->unit_count)
                                        {{ $order->unit_count }} pcs
                                    @else
                                        <span class="text-amber-600 font-medium italic">Belum ditimbang</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Courier -->
                            <td class="px-6 py-4">
                                @if($order->courier)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold">
                                            {{ substr($order->courier->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-semibold text-slate-800">{{ $order->courier->name }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $order->courier->phone }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ditugaskan</span>
                                @endif
                            </td>

                            <!-- Payment -->
                            <td class="px-6 py-4">
                                @if($order->payment_status === 'paid')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <span class="material-symbols-outlined text-xs mr-1">check</span> Lunas ({{ strtoupper($order->payment_method) }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center space-x-1">
                                    <!-- Button Update Status -->
                                    <button @click="openStatusModal({{ json_encode($order) }})" 
                                            class="px-2.5 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded-lg text-xs font-semibold transition flex items-center" 
                                            title="Ubah Status & Timbang">
                                        <span class="material-symbols-outlined text-base mr-1">sync</span>
                                        Ubah Status
                                    </button>

                                    <!-- Button Assign Courier -->
                                    <button @click="openCourierModal({{ json_encode($order) }})" 
                                            class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition flex items-center" 
                                            title="Tugaskan Kurir">
                                        <span class="material-symbols-outlined text-base mr-1">directions_bike</span>
                                        Kurir
                                    </button>

                                    <!-- Button View Timeline -->
                                    <button @click="openTimelineModal({{ json_encode($order) }})" 
                                            class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition" 
                                            title="Lihat Timeline Tracking">
                                        <span class="material-symbols-outlined text-base">timeline</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                Belum ada pesanan aktif. Buka Simulator Mobile untuk membuat pesanan demo!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Master Services Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Katalog Layanan & Tarif Laundry</h2>
                <p class="text-xs text-slate-500">Daftar paket layanan yang ditampilkan pada aplikasi mobile pelanggan.</p>
            </div>
            <button @click="isNewServiceOpen = true" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold transition flex items-center">
                <span class="material-symbols-outlined text-base mr-1.5">add</span>
                Tambah Layanan Baru
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($services as $svc)
                <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-brand-200 hover:shadow-sm transition group">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-brand-100 text-brand-800">
                                {{ $svc->category }}
                            </span>
                            <h3 class="font-bold text-slate-900 text-sm mt-1.5">{{ $svc->name }}</h3>
                        </div>
                        <span class="material-symbols-outlined text-brand-600 bg-brand-50 p-2 rounded-lg text-lg">
                            {{ $svc->icon ?? 'local_laundry_service' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ $svc->description }}</p>
                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
                        <span class="font-extrabold text-brand-700 text-sm">
                            Rp {{ number_format($svc->price_per_unit, 0, ',', '.') }} / {{ $svc->unit }}
                        </span>
                        <span class="text-slate-400 font-medium">Est. {{ $svc->estimated_hours }} Jam</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL 1: Update Status & Penimbangan -->
    <div x-show="isStatusModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
         x-transition x-cloak>
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100" @click.away="isStatusModalOpen = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Update Status & Penimbangan</h3>
                    <p class="text-xs text-slate-500" x-text="'Pesanan: ' + selectedOrder?.order_code"></p>
                </div>
                <button @click="isStatusModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form :action="'/admin/orders/' + selectedOrder?.id + '/status'" method="POST" class="mt-5 space-y-4">
                @csrf

                <!-- Status Selector -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1.5">Pilih Tahap Status Baru</label>
                    <select name="status" x-model="selectedStatus" class="w-full text-sm border-slate-200 rounded-xl px-3 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                        <option value="pending">1. Menunggu Konfirmasi / Pending</option>
                        <option value="picked_up">2. Pakaian Dijemput Kurir</option>
                        <option value="weighing">3. Penimbangan & Kalkulasi Tagihan</option>
                        <option value="washing">4. Sedang Dicuci (Washing Machine)</option>
                        <option value="drying">5. Sedang Dikeringkan (Dryer)</option>
                        <option value="ironing">6. Sedang Disetrika Uap (Ironing)</option>
                        <option value="ready">7. Siap Diantar / Selesai Packing</option>
                        <option value="delivering">8. Dalam Pengantaran Kurir</option>
                        <option value="completed">9. Selesai (Diterima Pelanggan)</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>

                <!-- Weight Input (Appears during weighing or any time) -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1.5">Bobot Aktual (Kg)</label>
                        <input type="number" step="0.1" name="total_weight" :value="selectedOrder?.total_weight" placeholder="Contoh: 4.5"
                               class="w-full text-sm border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1.5">Status Pembayaran</label>
                        <select name="payment_status" class="w-full text-sm border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                            <option value="unpaid" :selected="selectedOrder?.payment_status === 'unpaid'">Belum Lunas</option>
                            <option value="paid" :selected="selectedOrder?.payment_status === 'paid'">Lunas (Paid)</option>
                        </select>
                    </div>
                </div>

                <!-- Notes Input -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1.5">Catatan Status (Ditampilkan ke Pelanggan)</label>
                    <textarea name="notes" rows="2" placeholder="Contoh: Pakaian sedang dicuci dengan deterjen antibakteri."
                              class="w-full text-sm border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="isStatusModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Tugaskan Kurir -->
    <div x-show="isCourierModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
         x-transition x-cloak>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100" @click.away="isCourierModalOpen = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Tugaskan Kurir</h3>
                    <p class="text-xs text-slate-500" x-text="'Pesanan: ' + selectedOrder?.order_code"></p>
                </div>
                <button @click="isCourierModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form :action="'/admin/orders/' + selectedOrder?.id + '/assign-courier'" method="POST" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1.5">Pilih Kurir Siaga</label>
                    <select name="courier_id" class="w-full text-sm border-slate-200 rounded-xl px-3 py-2.5 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                        @foreach($couriers as $c)
                            <option value="{{ $c->id }}" :selected="selectedOrder?.courier_id === {{ $c->id }}">
                                {{ $c->name }} ({{ $c->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="isCourierModalOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl shadow-sm">Tugaskan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: Timeline Tracking Modal -->
    <div x-show="isTimelineModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
         x-transition x-cloak>
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-xl border border-slate-100" @click.away="isTimelineModalOpen = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">timeline</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Riwayat & Timeline Tracking</h3>
                        <p class="text-xs text-slate-500" x-text="selectedOrder?.order_code + ' • ' + selectedOrder?.service?.name"></p>
                    </div>
                </div>
                <button @click="isTimelineModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Stepper Timeline List -->
            <div class="mt-6 space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                <template x-for="(log, idx) in selectedOrder?.status_logs" :key="idx">
                    <div class="flex items-start space-x-3">
                        <div class="flex flex-col items-center">
                            <div class="w-7 h-7 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs font-bold shadow-sm">
                                <span class="material-symbols-outlined text-sm">check</span>
                            </div>
                            <div x-show="idx < selectedOrder.status_logs.length - 1" class="w-0.5 h-10 bg-slate-200 mt-1"></div>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-3 flex-1 border border-slate-100">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-800" x-text="log.title || log.status"></span>
                                <span class="text-[10px] text-slate-400" x-text="formatDate(log.created_at)"></span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1" x-text="log.notes"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- MODAL 4: Tambah Layanan Baru -->
    <div x-show="isNewServiceOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4"
         x-transition x-cloak>
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100" @click.away="isNewServiceOpen = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-900 text-lg">Tambah Layanan Laundry Baru</h3>
                <button @click="isNewServiceOpen = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form action="{{ route('admin.services.store') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1.5">Nama Layanan</label>
                    <input type="text" name="name" required placeholder="Contoh: Cuci Karpet & Permadani"
                           class="w-full text-sm border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1.5">Kategori</label>
                        <select name="category" class="w-full text-sm border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                            <option value="kiloan">Kiloan</option>
                            <option value="satuan">Satuan</option>
                            <option value="dry_clean">Dry Clean</option>
                            <option value="express">Express</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1.5">Satuan</label>
                        <input type="text" name="unit" value="kg" placeholder="kg / pcs / meter"
                               class="w-full text-sm border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1.5">Tarif per Satuan (Rp)</label>
                        <input type="number" name="price_per_unit" required placeholder="12000"
                               class="w-full text-sm border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 uppercase mb-1.5">Estimasi Pengerjaan (Jam)</label>
                        <input type="number" name="estimated_hours" value="24" required
                               class="w-full text-sm border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1.5">Deskripsi Singkat</label>
                    <textarea name="description" rows="2" placeholder="Jelaskan keunggulan layanan..."
                              class="w-full text-sm border-slate-200 rounded-xl px-3 py-2 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-brand-500"></textarea>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="isNewServiceOpen = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold rounded-xl shadow-sm">Simpan Layanan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function dashboardData() {
        return {
            isStatusModalOpen: false,
            isCourierModalOpen: false,
            isTimelineModalOpen: false,
            isNewServiceOpen: false,
            selectedOrder: null,
            selectedStatus: 'pending',

            openStatusModal(order) {
                this.selectedOrder = order;
                this.selectedStatus = order.status;
                this.isStatusModalOpen = true;
            },

            openCourierModal(order) {
                this.selectedOrder = order;
                this.isCourierModalOpen = true;
            },

            openTimelineModal(order) {
                this.selectedOrder = order;
                this.isTimelineModalOpen = true;
            },

            formatDate(str) {
                if (!str) return '';
                const d = new Date(str);
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            }
        }
    }
</script>
@endsection

