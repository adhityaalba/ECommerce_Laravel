<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    // Menampilkan halaman pembayaran dengan invoice
    public function index()
    {
        $cart = Session::get('cart', []);
        $total = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        return view('payment.index', compact('cart', 'total'));
    }

    // Mengunggah bukti pembayaran
    public function uploadPaymentProof(Request $request)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Menyimpan gambar bukti pembayaran
        $paymentProof = $request->file('payment_proof');
        $path = $paymentProof->store('payment_proofs', 'public');  // Menyimpan file di folder storage/public/payment_proofs

        // Menyimpan data pembayaran ke database
        $payment = new Payment();
        $payment->user_id = auth()->id();  // Mengambil id user yang sedang login
        $payment->payment_proof = $path;
        $payment->status = 'pending';
        $payment->save();

        // Menyimpan informasi pembayaran di session untuk keperluan checkout
        Session::put('payment_status', 'pending');

        // Kosongkan keranjang setelah pembayaran berhasil
        Session::forget('cart');  // Menghapus session cart

        // Redirect ke halaman pembayaran dengan pesan sukses
        return redirect()->route('payment')->with('success', 'Bukti pembayaran berhasil diunggah dan keranjang telah dikosongkan.');
    }
}