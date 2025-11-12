<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Booking;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat admin user
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        // Jalankan CategorySeeder terlebih dahulu
        $this->call(CategorySeeder::class);

        // Ambil kategori yang sudah dibuat dari seeder
        $lensa = Category::where('nama', 'Lensa')->first();
        $bodyOnly = Category::where('nama', 'Body Only')->first();
        $flash = Category::where('nama', 'Flash')->first();

        // Produk dummy
        $products = [
            [
                'nama' => 'Kamera Sony A7 III',
                'deskripsi' => 'Kamera full-frame cocok untuk foto dan video.',
                'gambar' => null,
                'harga_sewa_perhari' => 150000,
                'is_available' => true,
                'category_id' => $bodyOnly->id ?? null,
            ],
            [
                'nama' => 'Canon 5D Mark IV',
                'deskripsi' => 'Kamera DSLR profesional untuk produksi.',
                'gambar' => null,
                'harga_sewa_perhari' => 170000,
                'is_available' => true,
                'category_id' => $bodyOnly->id ?? null,
            ],
            [
                'nama' => 'Lensa Sony 24-70mm f/2.8',
                'deskripsi' => 'Lensa versatile untuk berbagai kebutuhan.',
                'gambar' => null,
                'harga_sewa_perhari' => 90000,
                'is_available' => true,
                'category_id' => $lensa->id ?? null,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Dummy booking
        Booking::create([
            'product_id' => 1,
            'nama_pemesan' => 'Budi Santoso',
            'nomor_wa' => '081234567890',
            'tanggal_mulai' => now()->addDays(1)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(3)->format('Y-m-d'),
            'status' => 'approved',
            'total_harga' => 150000 * 2,
            'catatan' => 'Untuk event wedding',
        ]);

        Booking::create([
            'product_id' => 2,
            'nama_pemesan' => 'Sinta Dewi',
            'nomor_wa' => '089876543210',
            'tanggal_mulai' => now()->addDays(5)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(6)->format('Y-m-d'),
            'status' => 'pending',
            'total_harga' => 170000 * 1,
            'catatan' => null,
        ]);
    }
}
