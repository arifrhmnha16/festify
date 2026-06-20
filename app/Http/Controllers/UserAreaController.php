<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use App\Models\ETicket;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TicketZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserAreaController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('concert', 'ticketZone', 'payment', 'eTickets.wristband')->latest()->limit(5)->get();
        $concerts = Concert::where('status', 'aktif')->latest('date')->limit(3)->get();
        return view('user.dashboard', compact('user', 'orders', 'concerts'));
    }

    public function concerts(Request $request)
    {
        $concerts = Concert::where('status', 'aktif')
            ->when($request->q, fn ($q, $term) => $q->where('name', 'like', "%{$term}%")->orWhere('artist', 'like', "%{$term}%"))
            ->when($request->venue, fn ($q, $venue) => $q->where('venue', 'like', "%{$venue}%"))
            ->when($request->date, fn ($q, $date) => $q->whereDate('date', $date))
            ->paginate(9)->withQueryString();
        return view('user.concerts', compact('concerts'));
    }

    public function concert(Concert $concert)
    {
        $concert->load('ticketZones');
        return view('user.concert-show', compact('concert'));
    }

    public function checkout(Concert $concert)
    {
        $concert->load('ticketZones');
        return view('user.checkout', compact('concert'));
    }

    public function storeOrder(Request $request, Concert $concert)
    {
        $data = $request->validate([
            'ticket_zone_id' => ['required', 'exists:ticket_zones,id'],
            'ticket_quantity' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $order = DB::transaction(function () use ($concert, $data) {
            $concert = Concert::whereKey($concert->id)->lockForUpdate()->firstOrFail();
            $zone = TicketZone::where('concert_id', $concert->id)
                ->whereKey($data['ticket_zone_id'])
                ->lockForUpdate()
                ->firstOrFail();

            abort_if($concert->stock < $data['ticket_quantity'] || $zone->stock < $data['ticket_quantity'], 422, 'Stok tiket tidak cukup.');

            $order = Order::create([
                'user_id' => Auth::id(),
                'concert_id' => $concert->id,
                'ticket_zone_id' => $zone->id,
                'order_code' => 'ORD-'.now()->format('ymd').'-'.Str::upper(Str::random(6)),
                'order_date' => now(),
                'ticket_quantity' => $data['ticket_quantity'],
                'total_price' => $zone->price * $data['ticket_quantity'],
            ]);
            Payment::create([
                'order_id' => $order->id,
                'total_amount' => $order->total_price,
            ]);
            $concert->decrement('stock', $data['ticket_quantity']);
            $zone->decrement('stock', $data['ticket_quantity']);
            return $order;
        });

        return redirect()->route('user.payments.show', $order);
    }

    public function orders()
    {
        $orders = Auth::user()->orders()->with('concert', 'ticketZone', 'payment', 'eTickets')->latest()->paginate(10);
        return view('user.orders', compact('orders'));
    }

    public function order(Order $order)
    {
        $this->own($order);
        $order->load('concert.ticketZones', 'ticketZone', 'payment', 'eTickets.wristband');
        return view('user.order-show', compact('order'));
    }

    public function payment(Order $order)
    {
        $this->own($order);
        $order->load('concert', 'ticketZone', 'payment');
        return view('user.payment', compact('order'));
    }

    public function submitPayment(Request $request, Order $order)
    {
        $this->own($order);
        $data = $request->validate([
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        abort_if($order->payment?->payment_status === 'success', 422, 'Pembayaran sudah diverifikasi.');

        $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        $payment = $order->payment ?: $order->payment()->create(['total_amount' => $order->total_price]);

        $payment->update([
            'payment_method' => $data['payment_method'],
            'payment_proof' => $proofPath,
            'payment_status' => 'pending',
            'payment_date' => now(),
        ]);

        $order->update(['order_status' => 'pending']);

        return redirect()->route('user.orders.show', $order)->with('success', 'Bukti pembayaran berhasil dikirim. Admin akan memverifikasi sebelum E-Ticket diterbitkan.');
    }

    public function tickets()
    {
        $tickets = Auth::user()->eTickets()->with('concert', 'order.ticketZone', 'order.payment', 'wristband')->latest()->paginate(10);
        return view('user.e-tickets', compact('tickets'));
    }

    public function ticket(ETicket $ticket)
    {
        abort_if($ticket->user_id !== Auth::id(), 403);
        $ticket->load('concert.ticketZones', 'order.ticketZone', 'user', 'wristband');
        return view('user.e-ticket-show', compact('ticket'));
    }

    private function own(Order $order): void
    {
        abort_if($order->user_id !== Auth::id(), 403);
    }

    private function issueTickets(Order $order): void
    {
        for ($i = $order->eTickets()->count(); $i < $order->ticket_quantity; $i++) {
            $code = 'TIX-'.now()->format('ymd').'-'.Str::upper(Str::random(8));
            ETicket::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'concert_id' => $order->concert_id,
                'ticket_code' => $code,
                'ticket_qr_code' => $code,
                'issued_at' => now(),
            ]);
        }
    }
}
