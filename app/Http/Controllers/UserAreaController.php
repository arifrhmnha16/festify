<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use App\Models\ETicket;
use App\Models\Order;
use App\Models\Payment;
use App\Models\TicketZone;
use App\Services\MidtransService;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $stats = [
            'Pesanan aktif' => $user->orders()->whereIn('order_status', ['pending', 'paid'])->count(),
            'Menunggu bayar' => $user->orders()->whereHas('payment', fn ($query) => $query->where('payment_status', 'pending'))->count(),
            'E-Ticket aktif' => $user->eTickets()->where('ticket_status', 'belum_ditukar')->count(),
            'Gelang aktif' => $user->eTickets()->whereHas('wristband', fn ($query) => $query->where('wristband_status', 'aktif'))->count(),
        ];

        return view('user.dashboard', compact('user', 'orders', 'concerts', 'stats'));
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
                'payment_method' => 'midtrans',
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

    public function payment(Order $order, MidtransService $midtrans)
    {
        $this->own($order);
        $order->load('concert', 'ticketZone', 'payment');
        $midtransReady = filled(config('services.midtrans.server_key')) && filled(config('services.midtrans.client_key'));

        if ($midtransReady && $order->payment?->payment_status !== 'success') {
            try {
                $payment = $midtrans->ensureSnapToken($order);
                $order->setRelation('payment', $payment);
            } catch (\Throwable $exception) {
                report($exception);

                return view('user.payment', compact('order', 'midtransReady'))
                    ->with('midtransError', 'Snap Midtrans belum bisa dibuat. Periksa server key/client key sandbox.');
            }
        }

        return view('user.payment', compact('order', 'midtransReady'));
    }

    public function submitPayment(Request $request, Order $order)
    {
        $this->own($order);

        return redirect()->route('user.payments.show', $order)->with('error', 'Pembayaran sekarang memakai Midtrans Sandbox. Silakan klik Bayar dengan Midtrans.');
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

    public function downloadTicket(Request $request, ETicket $ticket)
    {
        abort_if($ticket->user_id !== Auth::id(), 403);
        $ticket->load('concert', 'order.ticketZone', 'user', 'wristband');

        $pdf = Pdf::loadView('pdf.e-ticket', compact('ticket'))->setPaper('a4');
        $filename = $ticket->ticket_code.'.pdf';

        if ($request->query('mode') === 'print') {
            return $pdf->stream($filename);
        }

        return $pdf->download($filename);
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
