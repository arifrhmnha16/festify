<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Concert;
use App\Models\ETicket;
use App\Models\Officer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ScanHistory;
use App\Models\TicketZone;
use App\Models\User;
use App\Models\Wristband;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'Total konser' => Concert::count(),
            'Total user' => User::count(),
            'Total pemesanan' => Order::count(),
            'Pembayaran berhasil' => Payment::where('payment_status', 'success')->count(),
            'E-Ticket terbit' => ETicket::count(),
            'Gelang aktif' => Wristband::where('wristband_status', 'aktif')->count(),
            'Masuk gate' => Wristband::where('wristband_status', 'sudah_masuk')->count(),
        ];
        $orders = Order::with('user', 'concert', 'ticketZone', 'payment')->latest()->limit(6)->get();
        return view('admin.dashboard', compact('stats', 'orders'));
    }

    public function concerts()
    {
        $concerts = Concert::latest()->paginate(10);
        return view('admin.concerts', compact('concerts'));
    }

    public function storeConcert(Request $request)
    {
        $concert = Concert::create($this->concertData($request) + ['admin_id' => Auth::guard('admin')->id()]);
        $this->createDefaultZones($concert);
        return back()->with('success', 'Konser ditambahkan.');
    }

    public function updateConcert(Request $request, Concert $concert)
    {
        $concert->update($this->concertData($request, $concert));
        return back()->with('success', 'Konser diperbarui.');
    }

    public function destroyConcert(Concert $concert)
    {
        $concert->delete();
        return back()->with('success', 'Konser dihapus.');
    }

    public function featureConcert(Concert $concert)
    {
        Concert::query()->update(['is_featured' => false]);
        $concert->update(['is_featured' => true, 'status' => 'aktif']);

        return back()->with('success', 'Konser unggulan diperbarui.');
    }

    public function users()
    {
        return view('admin.users', ['users' => User::latest()->paginate(12)]);
    }

    public function storeUser(Request $request)
    {
        User::create($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'min:6'],
        ]));

        return back()->with('success', 'User ditambahkan.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'min:6'],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);
        return back()->with('success', 'User diperbarui.');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'User dihapus.');
    }

    public function officers()
    {
        return view('admin.officers', ['officers' => Officer::latest()->paginate(12)]);
    }

    public function storeOfficer(Request $request)
    {
        Officer::create($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', 'unique:officers,username'],
            'password' => ['required', 'min:6'],
            'role' => ['required', 'in:loket,gate'],
        ]));
        return back()->with('success', 'Petugas ditambahkan.');
    }

    public function updateOfficer(Request $request, Officer $officer)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:100', Rule::unique('officers', 'username')->ignore($officer)],
            'password' => ['nullable', 'min:6'],
            'role' => ['required', 'in:loket,gate'],
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $officer->update($data);
        return back()->with('success', 'Petugas diperbarui.');
    }

    public function destroyOfficer(Officer $officer)
    {
        $officer->delete();
        return back()->with('success', 'Petugas dihapus.');
    }

    public function orders()
    {
        return view('admin.orders', [
            'orders' => Order::with('user', 'concert', 'ticketZone', 'payment')->latest()->paginate(12),
            'users' => User::orderBy('name')->get(),
            'zones' => TicketZone::with('concert')->orderBy('concert_id')->orderBy('position')->get(),
        ]);
    }

    public function storeOrder(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'ticket_zone_id' => ['required', 'exists:ticket_zones,id'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
            'order_status' => ['required', 'in:pending,paid,cancelled,expired'],
        ]);

        $zone = TicketZone::with('concert')->findOrFail($data['ticket_zone_id']);
        $order = Order::create([
            'user_id' => $data['user_id'],
            'concert_id' => $zone->concert_id,
            'ticket_zone_id' => $zone->id,
            'order_code' => 'ORD-ADM-'.Str::upper(Str::random(6)),
            'order_date' => now(),
            'ticket_quantity' => $data['ticket_quantity'],
            'total_price' => $zone->price * $data['ticket_quantity'],
            'order_status' => $data['order_status'],
        ]);
        Payment::create([
            'order_id' => $order->id,
            'total_amount' => $order->total_price,
            'payment_status' => $data['order_status'] === 'paid' ? 'success' : 'pending',
            'payment_date' => now(),
        ]);

        return back()->with('success', 'Pemesanan ditambahkan.');
    }

    public function updateOrder(Request $request, Order $order)
    {
        $order->update($request->validate([
            'order_status' => ['required', 'in:pending,paid,cancelled,expired'],
        ]));

        return back()->with('success', 'Pemesanan diperbarui.');
    }

    public function destroyOrder(Order $order)
    {
        $order->delete();
        return back()->with('success', 'Pemesanan dihapus.');
    }

    public function payments()
    {
        return view('admin.payments', [
            'payments' => Payment::with('order.user', 'order.concert', 'order.ticketZone')->latest()->paginate(12),
            'orders' => Order::doesntHave('payment')->with('user', 'concert')->latest()->get(),
        ]);
    }

    public function storePayment(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'exists:orders,id', 'unique:payments,order_id'],
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_status' => ['required', 'in:pending,success,failed'],
        ]);
        $order = Order::findOrFail($data['order_id']);
        Payment::create($data + ['total_amount' => $order->total_price, 'payment_date' => now()]);

        return back()->with('success', 'Pembayaran ditambahkan.');
    }

    public function updatePayment(Request $request, Payment $payment)
    {
        $data = $request->validate(['payment_status' => ['required', 'in:pending,success,failed']]);
        $payment->update($data + ['payment_date' => now()]);

        if ($data['payment_status'] === 'success') {
            $order = $payment->order()->with('concert', 'ticketZone')->first();
            $order->update(['order_status' => 'paid']);
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
        } elseif ($data['payment_status'] === 'failed') {
            $payment->order->update(['order_status' => 'cancelled']);
        } else {
            $payment->order->update(['order_status' => 'pending']);
        }

        return back()->with('success', 'Pembayaran diperbarui.');
    }

    public function destroyPayment(Payment $payment)
    {
        $payment->delete();
        return back()->with('success', 'Pembayaran dihapus.');
    }

    public function eTickets()
    {
        return view('admin.e-tickets', [
            'tickets' => ETicket::with('user', 'concert', 'order')->latest()->paginate(12),
            'orders' => Order::with('user', 'concert')->latest()->get(),
        ]);
    }

    public function storeETicket(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'ticket_status' => ['required', 'in:belum_ditukar,sudah_ditukar,invalid'],
        ]);
        $order = Order::findOrFail($data['order_id']);
        $code = 'TIX-ADM-'.Str::upper(Str::random(8));
        ETicket::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'concert_id' => $order->concert_id,
            'ticket_code' => $code,
            'ticket_qr_code' => $code,
            'ticket_status' => $data['ticket_status'],
            'issued_at' => now(),
        ]);

        return back()->with('success', 'E-Ticket ditambahkan.');
    }

    public function updateETicket(Request $request, ETicket $ticket)
    {
        $ticket->update($request->validate([
            'ticket_status' => ['required', 'in:belum_ditukar,sudah_ditukar,invalid'],
        ]));

        return back()->with('success', 'E-Ticket diperbarui.');
    }

    public function destroyETicket(ETicket $ticket)
    {
        $ticket->delete();
        return back()->with('success', 'E-Ticket dihapus.');
    }

    public function wristbands()
    {
        return view('admin.wristbands', [
            'wristbands' => Wristband::with('eTicket.user', 'concert')->latest()->paginate(12),
            'tickets' => ETicket::doesntHave('wristband')->with('user', 'concert')->latest()->get(),
        ]);
    }

    public function storeWristband(Request $request)
    {
        $data = $request->validate([
            'e_ticket_id' => ['required', 'exists:e_tickets,id', 'unique:wristbands,e_ticket_id'],
            'wristband_status' => ['required', 'in:aktif,sudah_masuk,invalid'],
        ]);
        $ticket = ETicket::findOrFail($data['e_ticket_id']);
        $code = 'GLG-ADM-'.Str::upper(Str::random(8));
        Wristband::create([
            'e_ticket_id' => $ticket->id,
            'concert_id' => $ticket->concert_id,
            'wristband_code' => $code,
            'wristband_qr_code' => $code,
            'wristband_status' => $data['wristband_status'],
            'activated_at' => now(),
        ]);

        return back()->with('success', 'Gelang ditambahkan.');
    }

    public function updateWristband(Request $request, Wristband $wristband)
    {
        $wristband->update($request->validate([
            'wristband_status' => ['required', 'in:aktif,sudah_masuk,invalid'],
        ]));

        return back()->with('success', 'Gelang diperbarui.');
    }

    public function destroyWristband(Wristband $wristband)
    {
        $wristband->delete();
        return back()->with('success', 'Gelang dihapus.');
    }

    public function reports()
    {
        $histories = ScanHistory::with('officer', 'eTicket.user', 'wristband')->latest('scanned_at')->paginate(20);
        return view('admin.reports', compact('histories'));
    }

    public function destroyScanHistory(ScanHistory $history)
    {
        $history->delete();
        return back()->with('success', 'Riwayat scan dihapus.');
    }

    private function concertData(Request $request, ?Concert $concert = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'artist' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'venue' => ['required', 'string', 'max:150'],
            'date' => ['required', 'date'],
            'time' => ['required'],
            'poster' => ['nullable', 'image', 'max:2048'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'seat_zone' => ['nullable', 'string', 'max:80'],
            'status' => ['required', 'in:aktif,selesai,dibatalkan'],
        ]);

        if ($request->hasFile('poster')) {
            $data['poster'] = $request->file('poster')->store('posters', 'public');
        } elseif ($concert) {
            unset($data['poster']);
        }

        return $data;
    }

    private function createDefaultZones(Concert $concert): void
    {
        $zones = [
            ['name' => 'Festival Depan', 'price' => (int) round($concert->price * 1.25), 'stock' => max(1, (int) floor($concert->stock * 0.3)), 'color' => '#dc2626', 'position' => 1],
            ['name' => 'Festival Tengah', 'price' => $concert->price, 'stock' => max(1, (int) floor($concert->stock * 0.45)), 'color' => '#ea580c', 'position' => 2],
            ['name' => 'Tribune Belakang', 'price' => (int) round($concert->price * 0.8), 'stock' => max(1, (int) ceil($concert->stock * 0.25)), 'color' => '#ca8a04', 'position' => 3],
        ];

        foreach ($zones as $zone) {
            $concert->ticketZones()->create($zone);
        }
    }
}
