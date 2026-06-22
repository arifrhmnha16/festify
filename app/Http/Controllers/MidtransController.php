<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MidtransController extends Controller
{
    public function notification(Request $request, MidtransService $midtrans)
    {
        $payload = $request->all();

        abort_unless($midtrans->validSignature($payload), 403, 'Invalid Midtrans signature.');

        $payment = $midtrans->syncFromStatusObject($payload);

        return response()->json([
            'message' => 'Notification accepted.',
            'order_code' => $payment->order->order_code,
            'payment_status' => $payment->payment_status,
        ]);
    }

    public function finish(Request $request, Order $order, MidtransService $midtrans)
    {
        if ($order->payment?->gateway_order_id) {
            try {
                $midtrans->syncFromMidtrans($order->payment);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        if (Auth::check() && $order->user_id === Auth::id()) {
            return redirect()->route('user.orders.show', $order)->with('success', 'Status pembayaran sedang disinkronkan dari Midtrans.');
        }

        return redirect()->route('login')->with('success', 'Pembayaran diproses. Silakan login untuk melihat pesanan.');
    }

    public function sync(Order $order, MidtransService $midtrans)
    {
        abort_if($order->user_id !== Auth::id(), 403);
        abort_unless($order->payment?->gateway_order_id, 404, 'Transaksi Midtrans belum dibuat.');

        try {
            $payment = $midtrans->syncFromMidtrans($order->payment);
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Status Midtrans belum tersedia. Coba lagi setelah memilih metode pembayaran di Snap.');
        }

        return back()->with('success', "Status pembayaran: {$payment->payment_status}.");
    }
}
