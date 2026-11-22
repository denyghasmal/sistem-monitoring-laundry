<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::create([
            'name'      => 'Admin Gerai Laundry',
            'email'     => 'admin@laundry.com',
            'phone'     => '081234567890',
            'role'      => 'admin',
            'password'  => Hash::make('password'),
            'avatar'    => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150',
            'is_active' => true,
        ]);

        $courier1 = User::create([
            'name'      => 'Bambang Pamungkas (Kurir 1)',
            'email'     => 'kurir@laundry.com',
            'phone'     => '081298765432',
            'role'      => 'courier',
            'password'  => Hash::make('password'),
            'avatar'    => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150',
            'is_active' => true,
        ]);

        $courier2 = User::create([
            'name'      => 'Rian Hidayat (Kurir Express)',
            'email'     => 'kurir2@laundry.com',
            'phone'     => '081377889900',
            'role'      => 'courier',
            'password'  => Hash::make('password'),
            'avatar'    => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150',
            'is_active' => true,
        ]);

        $customer1 = User::create([
            'name'      => 'Budi Santoso',
            'email'     => 'budi@gmail.com',
            'phone'     => '085711223344',
            'role'      => 'customer',
            'password'  => Hash::make('password'),
            'avatar'    => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150',
            'is_active' => true,
        ]);

        $customer2 = User::create([
            'name'      => 'Siti Rahmawati',
            'email'     => 'siti@gmail.com',
            'phone'     => '085799887766',
            'role'      => 'customer',
            'password'  => Hash::make('password'),
            'avatar'    => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150',
            'is_active' => true,
        ]);

        // 2. Addresses
        $addrBudiHome = Address::create([
            'user_id'         => $customer1->id,
            'address_label'   => 'Rumah',
            'recipient_name'  => 'Budi Santoso',
            'recipient_phone' => '085711223344',
            'full_address'    => 'Jl. Sudirman No. 45, RT 02 / RW 05, Menteng, Jakarta Pusat',
            'latitude'        => -6.214620,
            'longitude'       => 106.845130,
            'notes'           => 'Pagar warna hitam, samping minimarket Alfamart.',
            'is_primary'      => true,
        ]);

        $addrBudiOffice = Address::create([
            'user_id'         => $customer1->id,
            'address_label'   => 'Kantor',
            'recipient_name'  => 'Budi Santoso (Divisi IT)',
            'recipient_phone' => '085711223344',
            'full_address'    => 'Gedung Cyber 2 Tower Lt. 12, Jl. HR Rasuna Said Blok X-5 No. 13, Jakarta Selatan',
            'latitude'        => -6.229728,
            'longitude'       => 106.830508,
            'notes'           => 'Titipkan ke resepsionis lobi utama lantai dasar.',
            'is_primary'      => false,
        ]);

        $addrSiti = Address::create([
            'user_id'         => $customer2->id,
            'address_label'   => 'Apartemen',
            'recipient_name'  => 'Siti Rahmawati',
            'recipient_phone' => '085799887766',
            'full_address'    => 'Apartemen Kalibata City Tower Kemuning Lt. 8 No. 12B, Rawajati, Pancoran, Jakarta Selatan',
            'latitude'        => -6.257400,
            'longitude'       => 106.855200,
            'notes'           => 'Hubungi via WhatsApp sebelum naik ke unit.',
            'is_primary'      => true,
        ]);

        // 3. Services Catalog
        $svcKiloan = Service::create([
            'name'            => 'Cuci Komplit Reguler (Cuci, Kering, Setrika)',
            'slug'            => 'cuci-komplit-reguler',
            'category'        => 'kiloan',
            'price_per_unit'  => 8000,
            'unit'            => 'kg',
            'estimated_hours' => 48,
            'description'     => 'Layanan komprehensif mencakup pencucian higienis dengan deterjen premium, pengeringan mesin, dan penyetrikaan uap halus serta parfum pilihan.',
            'icon'            => 'wash',
            'is_active'       => true,
        ]);

        $svcExpress = Service::create([
            'name'            => 'Cuci Kilat Express (6 Jam Selesai)',
            'slug'            => 'cuci-kilat-express',
            'category'        => 'express',
            'price_per_unit'  => 15000,
            'unit'            => 'kg',
            'estimated_hours' => 6,
            'description'     => 'Solusi cepat untuk kebutuhan mendesak! Pakaian siap dalam waktu 6 jam dengan penanganan prioritas tertinggi.',
            'icon'            => 'flash_on',
            'is_active'       => true,
        ]);

        $svcJas = Service::create([
            'name'            => 'Satuan Jas / Blazer',
            'slug'            => 'cuci-setrika-jas-blazer',
            'category'        => 'satuan',
            'price_per_unit'  => 35000,
            'unit'            => 'pcs',
            'estimated_hours' => 24,
            'description'     => 'Perawatan khusus jas dan blazer formal. Termasuk steamer uap vertikal dan hanger pelindung debu.',
            'icon'            => 'dry_cleaning',
            'is_active'       => true,
        ]);

        $svcBedCover = Service::create([
            'name'            => 'Bed Cover & Selimut Jumbo',
            'slug'            => 'bed-cover-selimut-jumbo',
            'category'        => 'satuan',
            'price_per_unit'  => 40000,
            'unit'            => 'pcs',
            'estimated_hours' => 48,
            'description'     => 'Pencucian bed cover king/queen size dengan mesin kapasitas besar dan pengeringan optimal bebas bau apek.',
            'icon'            => 'bed',
            'is_active'       => true,
        ]);

        $svcDryClean = Service::create([
            'name'            => 'Dry Clean Gaun / Kebaya Sutra',
            'slug'            => 'dry-clean-gaun-kebaya',
            'category'        => 'dry_clean',
            'price_per_unit'  => 55000,
            'unit'            => 'pcs',
            'estimated_hours' => 72,
            'description'     => 'Metode pencucian tanpa air (dry cleaning) khusus pakaian berbahan sensitif seperti sutra, renda, dan payet halus.',
            'icon'            => 'checkroom',
            'is_active'       => true,
        ]);

        $svcSetrika = Service::create([
            'name'            => 'Setrika Uap Kiloan Saja',
            'slug'            => 'setrika-uap-kiloan',
            'category'        => 'kiloan',
            'price_per_unit'  => 5000,
            'unit'            => 'kg',
            'estimated_hours' => 24,
            'description'     => 'Pakaian sudah bersih dari rumah? Kami bantu setrika uap rapi, licin, wangi, dan dilipat rapi.',
            'icon'            => 'iron',
            'is_active'       => true,
        ]);

        // 4. Sample Orders

        // Order 1: Status 'washing' (sedang dicuci)
        $order1 = Order::create([
            'order_code'          => 'LND-20261103-W01',
            'customer_id'         => $customer1->id,
            'courier_id'          => $courier1->id,
            'service_id'          => $svcKiloan->id,
            'address_id'          => $addrBudiHome->id,
            'delivery_type'       => 'pickup_delivery',
            'total_weight'        => 5.2,
            'unit_count'          => null,
            'price_per_unit'      => 8000,
            'subtotal'            => 41600,
            'delivery_fee'        => 10000,
            'total_price'         => 51600,
            'status'              => 'washing',
            'payment_status'      => 'paid',
            'payment_method'      => 'qris',
            'payment_reference'   => 'PAY-QRIS-99120',
            'paid_at'             => now()->subHours(2),
            'pickup_scheduled_at' => now()->subHours(4),
            'customer_notes'      => 'Tolong pisahkan pakaian warna putih.',
            'admin_notes'         => 'Total bobot aktual: 5.2 kg. Sudah dipilah.',
            'created_at'          => now()->subHours(4),
        ]);

        $order1->statusLogs()->createMany([
            [
                'status'     => 'pending',
                'title'      => 'Pesanan Diterima',
                'notes'      => 'Pesanan berhasil dibuat oleh pelanggan.',
                'created_by' => $customer1->id,
                'created_at' => now()->subHours(4),
            ],
            [
                'status'     => 'picked_up',
                'title'      => 'Pakaian Dijemput',
                'notes'      => 'Kurir Bambang Pamungkas telah mengambil pakaian di Jl. Sudirman No. 45.',
                'created_by' => $courier1->id,
                'created_at' => now()->subHours(3)->addMinutes(15),
            ],
            [
                'status'     => 'weighing',
                'title'      => 'Penimbangan & Tagihan',
                'notes'      => 'Pakaian telah ditimbang oleh kasir gerai (5.2 kg). Tagihan Rp 51.600 telah diverifikasi.',
                'created_by' => $admin->id,
                'created_at' => now()->subHours(2)->addMinutes(30),
            ],
            [
                'status'     => 'washing',
                'title'      => 'Sedang Dicuci',
                'notes'      => 'Pakaian saat ini sedang diproses di mesin cuci washer #3 dengan deterjen antibakteri.',
                'created_by' => $admin->id,
                'created_at' => now()->subHours(1),
            ],
        ]);

        // Order 2: Status 'delivering' (sedang diantar kurir)
        $order2 = Order::create([
            'order_code'          => 'LND-20261103-D02',
            'customer_id'         => $customer2->id,
            'courier_id'          => $courier1->id,
            'service_id'          => $svcBedCover->id,
            'address_id'          => $addrSiti->id,
            'delivery_type'       => 'pickup_delivery',
            'total_weight'        => 3.0,
            'unit_count'          => 1,
            'price_per_unit'      => 40000,
            'subtotal'            => 40000,
            'delivery_fee'        => 10000,
            'total_price'         => 50000,
            'status'              => 'delivering',
            'payment_status'      => 'paid',
            'payment_method'      => 'bank_transfer',
            'payment_reference'   => 'PAY-VA-77821',
            'paid_at'             => now()->subHours(8),
            'customer_notes'      => 'Bed cover bahan katun jepang.',
            'admin_notes'         => 'Sudah diplastik rapi dan diberi wangi sakura.',
            'created_at'          => now()->subHours(24),
        ]);

        $order2->statusLogs()->createMany([
            ['status' => 'pending',    'title' => 'Pemesanan',       'notes' => 'Pesanan masuk via aplikasi mobile.', 'created_by' => $customer2->id, 'created_at' => now()->subHours(24)],
            ['status' => 'picked_up',  'title' => 'Penjemputan',     'notes' => 'Pakaian diambil kurir di Apartemen Kalibata.', 'created_by' => $courier1->id, 'created_at' => now()->subHours(22)],
            ['status' => 'weighing',   'title' => 'Penimbangan',     'notes' => '1 Pcs Bed Cover King Size diperiksa.', 'created_by' => $admin->id, 'created_at' => now()->subHours(20)],
            ['status' => 'washing',    'title' => 'Pencucian',       'notes' => 'Pencucian selesai.', 'created_by' => $admin->id, 'created_at' => now()->subHours(16)],
            ['status' => 'drying',     'title' => 'Pengeringan',     'notes' => 'Pengeringan mesin suhu 60 derajat.', 'created_by' => $admin->id, 'created_at' => now()->subHours(12)],
            ['status' => 'ironing',    'title' => 'Penyetrikaan',    'notes' => 'Disetrika uap dan dipacking vacuum.', 'created_by' => $admin->id, 'created_at' => now()->subHours(6)],
            ['status' => 'ready',      'title' => 'Siap Diantar',    'notes' => 'Pakaian siap di gerai untuk didistribusikan.', 'created_by' => $admin->id, 'created_at' => now()->subHours(2)],
            ['status' => 'delivering', 'title' => 'Sedang Diantar',  'notes' => 'Kurir Bambang sedang menuju lokasi pelanggan.', 'created_by' => $courier1->id, 'created_at' => now()->subMinutes(20)],
        ]);

        // Order 3: Status 'pending' (menunggu jemput)
        $order3 = Order::create([
            'order_code'          => 'LND-20261103-P03',
            'customer_id'         => $customer1->id,
            'courier_id'          => null,
            'service_id'          => $svcExpress->id,
            'address_id'          => $addrBudiOffice->id,
            'delivery_type'       => 'pickup_delivery',
            'total_weight'        => null,
            'unit_count'          => null,
            'price_per_unit'      => 15000,
            'subtotal'            => 45000,
            'delivery_fee'        => 10000,
            'total_price'         => 55000,
            'status'              => 'pending',
            'payment_status'      => 'unpaid',
            'payment_method'      => 'cash',
            'pickup_scheduled_at' => now()->addHour(),
            'customer_notes'      => 'Mohon kurir bawa kantong laundry cadangan.',
            'created_at'          => now()->subMinutes(15),
        ]);

        $order3->statusLogs()->create([
            'status'     => 'pending',
            'title'      => 'Pesanan Diterima',
            'notes'      => 'Pesanan dibuat. Menunggu penugasan kurir jemput.',
            'created_by' => $customer1->id,
            'created_at' => now()->subMinutes(15),
        ]);

        // Order 4: Status 'completed' with Review
        $order4 = Order::create([
            'order_code'          => 'LND-20261102-C04',
            'customer_id'         => $customer1->id,
            'courier_id'          => $courier2->id,
            'service_id'          => $svcJas->id,
            'address_id'          => $addrBudiHome->id,
            'delivery_type'       => 'pickup_delivery',
            'total_weight'        => null,
            'unit_count'          => 2,
            'price_per_unit'      => 35000,
            'subtotal'            => 70000,
            'delivery_fee'        => 10000,
            'total_price'         => 80000,
            'status'              => 'completed',
            'payment_status'      => 'paid',
            'payment_method'      => 'qris',
            'payment_reference'   => 'PAY-QRIS-88123',
            'paid_at'             => now()->subDays(1),
            'delivered_at'        => now()->subDays(1)->addHours(4),
            'created_at'          => now()->subDays(2),
        ]);

        $order4->statusLogs()->createMany([
            ['status' => 'pending',    'title' => 'Pemesanan',    'notes' => 'Pesanan diterima.', 'created_by' => $customer1->id, 'created_at' => now()->subDays(2)],
            ['status' => 'picked_up',  'title' => 'Penjemputan',  'notes' => 'Diambil oleh kurir Rian.', 'created_by' => $courier2->id, 'created_at' => now()->subDays(2)->addHours(2)],
            ['status' => 'ready',      'title' => 'Siap Diantar', 'notes' => 'Jas rapi dengan hanger.', 'created_by' => $admin->id, 'created_at' => now()->subDays(1)],
            ['status' => 'delivering', 'title' => 'Pengantaran',  'notes' => 'Sedang diantar ke Menteng.', 'created_by' => $courier2->id, 'created_at' => now()->subDays(1)->addHours(2)],
            ['status' => 'completed',  'title' => 'Selesai',      'notes' => 'Diterima oleh Budi Santoso.', 'created_by' => $courier2->id, 'created_at' => now()->subDays(1)->addHours(4)],
        ]);

        Review::create([
            'order_id'    => $order4->id,
            'customer_id' => $customer1->id,
            'rating'      => 5,
            'comment'     => 'Jas dan blazer saya jadi rapi sekali, wangi segar tahan lama dan tidak ada kusut sama sekali. Kurirnya ramah dan profesional!',
            'created_at'  => now()->subDays(1)->addHours(5),
        ]);
    }
}
