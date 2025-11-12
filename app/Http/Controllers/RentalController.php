<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RentalController extends Controller
{


    public function index()
    {
        $products = Product::where('is_available', true)
            ->withCount('bookings')
            ->get();
        return Inertia::render('Dashboard', [
            'products' => $products,
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
