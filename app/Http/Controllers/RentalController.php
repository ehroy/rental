<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Booking;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RentalController extends Controller
{


    public function index(Request $request)
    {
        $query = Product::where('is_available', true)
            ->with(['category'])
            ->withCount('bookings');

        // Filter berdasarkan kategori jika ada dan tidak kosong
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter berdasarkan search query
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->get();

        // Ambil semua kategori untuk ditampilkan sebagai filter
        $categories = Category::select('id', 'nama')->get();

        return Inertia::render('Dashboard', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'category_id' => $request->category_id ?? '',
                'search' => $request->search ?? '',
            ],
        ]);
    }
    public function show(Product $product)
    {
        $bookedDates = $product->getBookedDates();

        return Inertia::render('Rental/Show', [
            'product' => $product,
            'bookedDates' => $bookedDates,
        ]);
    }

    public function checkAvailability(Request $request, Product $product)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $available = $product->isAvailableOnDate(
            $request->tanggal_mulai,
            $request->tanggal_selesai,
        );

        if ($available) {
            $days = \Carbon\Carbon::parse($request->tanggal_mulai)
                ->diffInDays(\Carbon\Carbon::parse($request->tanggal_selesai)) + 1;

            $totalPrice = $product->harga_sewa_perhari * $days;

            return response()->json([
                'available' => true,
                'days' => $days,
                'total_price' => $totalPrice,
                'price_formatted' => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
            ]);
        }

        return response()->json([
            'available' => false,
            'message' => 'Produk sudah dibooking pada tanggal tersebut',
        ]);
    }

    public function bookingstore(Request $request, Product $product)
    {
        $validated = $request->validate([
            'nama_pemesan' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:20',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
        ]);

        if (!$product->isAvailableOnDate($request->tanggal_mulai, $request->tanggal_selesai)) {
            return back()->with('error', 'Produk sudah dibooking pada tanggal tersebut');
        }

        $days = \Carbon\Carbon::parse($request->tanggal_mulai)
            ->diffInDays(\Carbon\Carbon::parse($request->tanggal_selesai)) + 1;

        $totalPrice = $product->harga_sewa_perhari * $days * $request->jumlah;

        try {
            $booking = Booking::create([
                'product_id' => $product->id,
                'nama_pemesan' => $request->nama_pemesan,
                'nomor_wa' => $request->nomor_wa,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'jumlah' => $request->jumlah,
                'durasi_hari' => $days,
                'status' => 'pending',
                'total_harga' => $totalPrice,
                'catatan' => $request->catatan,
            ]);

            // Format WhatsApp message
            $adminPhone = '62895381587961';
            
            $message = "🎯 *BOOKING BARU*\n";
            $message .= "━━━━━━━━━━━━━━━━━━\n\n";
            $message .= "📦 *Produk:* {$product->nama}\n";
            $message .= "👤 *Nama Pemesan:* {$request->nama_pemesan}\n";
            $message .= "📱 *No. WhatsApp:* {$request->nomor_wa}\n\n";
            $message .= "📅 *Tanggal Mulai:*\n";
            $message .= "   " . \Carbon\Carbon::parse($request->tanggal_mulai)->isoFormat('dddd, D MMMM YYYY') . "\n\n";
            $message .= "📅 *Tanggal Selesai:*\n";
            $message .= "   " . \Carbon\Carbon::parse($request->tanggal_selesai)->isoFormat('dddd, D MMMM YYYY') . "\n\n";
            $message .= "⏱️ *Durasi Sewa:* {$days} Hari\n";
            $message .= "🔢 *Jumlah Unit:* {$request->jumlah}\n\n";
            $message .= "💰 *Rincian Harga:*\n";
            $message .= "   Rp " . number_format($product->harga_sewa_perhari, 0, ',', '.') . " x {$days} hari x {$request->jumlah} unit\n";
            $message .= "   *Total: Rp " . number_format($totalPrice, 0, ',', '.') . "*\n\n";
            
            if ($request->catatan) {
                $message .= "📝 *Catatan:*\n   {$request->catatan}\n\n";
            }
            
            $message .= "━━━━━━━━━━━━━━━━━━\n";
            $message .= "🆔 *Kode Booking:* #{$booking->id}\n";
            $message .= "⏰ *Waktu Order:* " . now()->isoFormat('D MMM YYYY, HH:mm') . "\n\n";
            $message .= "_Mohon konfirmasi ketersediaan dan pembayaran_";
            
            $encodedMessage = urlencode($message);
            $whatsappUrl = "https://wa.me/{$adminPhone}?text={$encodedMessage}";
            

            // CEK: Apakah request dari Inertia atau biasa?
            if ($request->header('X-Inertia')) {
                // Return Inertia response dengan whatsappUrl di props
                return \Inertia\Inertia::location($whatsappUrl);
            }
            
            // Jika request biasa (fetch/axios), return JSON
            return response()->json([
                'success' => true,
                'booking_id' => $booking->id,
                'whatsappUrl' => $whatsappUrl,
                'message' => 'Booking berhasil dibuat!'
            ]);
            
        } catch (\Exception $e) {

            if ($request->header('X-Inertia')) {
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cartCheckout(Request $request)
    {
       
        if ($request->has('cart_items')) {
        foreach ($request->cart_items as $item) {
            $product = \App\Models\Product::find($item['id']);
           
        }
        }

        $validated = $request->validate([
            'nama_pemesan' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:20',
            'cart_items' => 'required|array|min:1',
            'cart_items.*.id' => 'required|integer|exists:products,id',
            'cart_items.*.jumlah' => 'required|integer|min:1',
            'cart_items.*.tanggal_mulai' => 'required|date',
            'cart_items.*.tanggal_selesai' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        try {
            $totalHargaKeseluruhan = 0;
            $bookings = [];
            $productDetails = [];

            // Loop setiap item di cart
            foreach ($request->cart_items as $item) {
                $product = Product::findOrFail($item['id']);

                // Cek ketersediaan untuk tanggal masing-masing produk
                if (!$product->isAvailableOnDate($item['tanggal_mulai'], $item['tanggal_selesai'])) {
                    return back()->with('error', "Produk {$product->nama} sudah dibooking pada tanggal tersebut");
                }

                $days = \Carbon\Carbon::parse($item['tanggal_mulai'])
                    ->diffInDays(\Carbon\Carbon::parse($item['tanggal_selesai'])) + 1;

                $totalPrice = $product->harga_sewa_perhari * $days * $item['jumlah'];
                $totalHargaKeseluruhan += $totalPrice;

                // Simpan booking untuk setiap produk
                $booking = Booking::create([
                    'product_id' => $product->id,
                    'nama_pemesan' => $request->nama_pemesan,
                    'nomor_wa' => $request->nomor_wa,
                    'tanggal_mulai' => $item['tanggal_mulai'],
                    'tanggal_selesai' => $item['tanggal_selesai'],
                    'jumlah' => $item['jumlah'],
                    'durasi_hari' => $days,
                    'status' => 'pending',
                    'total_harga' => $totalPrice,
                    'catatan' => $request->catatan ?? null,
                ]);

                $bookings[] = $booking;
                $productDetails[] = [
                    'nama' => $product->nama,
                    'harga' => $product->harga_sewa_perhari,
                    'jumlah' => $item['jumlah'],
                    'tanggal_mulai' => $item['tanggal_mulai'],
                    'tanggal_selesai' => $item['tanggal_selesai'],
                    'durasi' => $days,
                    'total' => $totalPrice
                ];
            }

            // Format WhatsApp message untuk multiple items
            $adminPhone = '62895381587961';
            
            $message = "🎯 *BOOKING KERANJANG*\n";
            $message .= "━━━━━━━━━━━━━━━━━━\n\n";
            $message .= "👤 *Nama Pemesan:* {$request->nama_pemesan}\n";
            $message .= "📱 *No. WhatsApp:* {$request->nomor_wa}\n\n";
            
            $message .= "📦 *DAFTAR PRODUK:*\n";
            $message .= "━━━━━━━━━━━━━━━━━━\n";
            
            foreach ($productDetails as $index => $detail) {
                $message .= "\n*" . ($index + 1) . ". {$detail['nama']}*\n";
                $message .= "   🔢 Jumlah: {$detail['jumlah']} unit\n";
                $message .= "   📅 Mulai: " . \Carbon\Carbon::parse($detail['tanggal_mulai'])->isoFormat('D MMM YYYY') . "\n";
                $message .= "   📅 Selesai: " . \Carbon\Carbon::parse($detail['tanggal_selesai'])->isoFormat('D MMM YYYY') . "\n";
                $message .= "   ⏱️ Durasi: {$detail['durasi']} hari\n";
                $message .= "   💰 Harga: Rp " . number_format($detail['harga'], 0, ',', '.') . "/hari\n";
                $message .= "   💵 Subtotal: Rp " . number_format($detail['total'], 0, ',', '.') . "\n";
            }
            
            $message .= "\n━━━━━━━━━━━━━━━━━━\n";
            $message .= "💰 *TOTAL PEMBAYARAN:*\n";
            $message .= "   *Rp " . number_format($totalHargaKeseluruhan, 0, ',', '.') . "*\n\n";
            
            if ($request->catatan) {
                $message .= "📝 *Catatan:*\n   {$request->catatan}\n\n";
            }
            
            $message .= "━━━━━━━━━━━━━━━━━━\n";
            $message .= "🆔 *Kode Booking:*\n";
            foreach ($bookings as $booking) {
                $message .= "   #{$booking->id}\n";
            }
            $message .= "\n⏰ *Waktu Order:* " . now()->isoFormat('D MMM YYYY, HH:mm') . "\n\n";
            $message .= "_Mohon konfirmasi ketersediaan dan pembayaran_";
            
            $encodedMessage = urlencode($message);
            $whatsappUrl = "https://wa.me/{$adminPhone}?text={$encodedMessage}";

            // Return untuk Inertia
            if ($request->header('X-Inertia')) {
                return \Inertia\Inertia::location($whatsappUrl);
            }
            
            // Return JSON untuk fetch/axios
            return response()->json([
                'success' => true,
                'booking_ids' => array_map(fn($b) => $b->id, $bookings),
                'whatsappUrl' => $whatsappUrl,
                'message' => 'Booking berhasil dibuat!'
            ]);
            
        } catch (\Exception $e) {
            if ($request->header('X-Inertia')) {
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function orders()
    {
        $bookings = Booking::with('product')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Rental/Orders', [
            'bookings' => $bookings,
        ]);
    }
}
