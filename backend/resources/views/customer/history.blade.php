@extends('customer.layout')

@section('title', 'Riwayat Pesanan Laundry')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-brand-600 uppercase tracking-wider bg-brand-50 px-2.5 py-0.5 rounded-full border border-brand-200">
                Histori Cucian
            </span>
            <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Riwayat Transaksi Pelanggan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar semua pesanan laundry yang pernah Anda buat di LaundryHub.</p>
        </div>

        <a href="{{ route('customer.order') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition shadow-sm self-start sm:self-auto">
            <span class="material-symbols-outlined text-sm mr-1.5">add</span>
            Pesan Baru
        </a>
    </div>

    <!-- Orders Table or Empty State -->
    @if($orders->isEmpty())
        <div class="bg-white p-12 rounded-2xl border border-slate-200 shadow-sm text-center">
            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-400">
                <span class="material-symbols-outlined text-3xl">history_toggle_off</span>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Belum Ada Riwayat Pesanan</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mb-6">Anda belum pernah melakukan pemesanan. Mulai cuci pakaian Anda dengan mudah sekarang!</p>
            <a href="{{ route('customer.order') }}" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition">
                Pesan Laundry Sekarang
            </a>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 uppercase font-bold tracking-wider text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Kode Pesanan</th>
                            <th class="px-5 py-3.5">Layanan & Berat</th>
                            <th class="px-5 py-3.5">Tanggal</th>
                            <th class="px-5 py-3.5">Total Biaya</th>
                            <th class="px-5 py-3.5">Status Cucian</th>
                            <th class="px-5 py-3.5">Pembayaran</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($orders as $item)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-4">
                                    <span class="font-mono font-bold text-slate-900 block text-xs">{{ $item->order_code }}</span>
                                    <span class="text-[10px] text-slate-400 capitalize">{{ str_replace('_', ' ', $item->delivery_type) }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-semibold text-slate-900 block">{{ $item->service->name }}</span>
                                    <span class="text-[10px] text-slate-500">{{ $item->total_weight }} {{ $item->service->unit }}</span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-slate-500">
                                    {{ $item->created_at->format('d M Y') }}
                                    <span class="block text-[10px] text-slate-400">{{ $item->created_at->format('H:i') }} WIB</span>
                                </td>
                                <td class="px-5 py-4 font-bold text-slate-900 whitespace-nowrap">
                                    Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold inline-block
                                        @if(in_array($item->status, ['completed'])) bg-emerald-100 text-emerald-800
                                        @elseif(in_array($item->status, ['cancelled'])) bg-rose-100 text-rose-800
                                        @elseif(in_array($item->status, ['washing', 'ironing', 'packing'])) bg-indigo-100 text-indigo-800
                                        @elseif(in_array($item->status, ['picking_up', 'delivering'])) bg-amber-100 text-amber-800
                                        @else bg-sky-100 text-sky-800
                                        @endif">
                                        {{ $item->status_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold inline-block {{ $item->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                        {{ strtoupper($item->payment_status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('customer.track', ['code' => $item->order_code]) }}" 
                                       class="inline-flex items-center px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded-lg font-bold text-xs transition">
                                        <span class="material-symbols-outlined text-sm mr-1">timeline</span>
                                        Lacak
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $orders->links() }}
            </div>
        </div>
    @endif

</div>
@endsection

