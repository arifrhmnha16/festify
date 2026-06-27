<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use App\Models\ETicket;
use App\Models\Officer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ScanHistory;
use App\Models\SiteSetting;
use App\Models\TicketZone;
use App\Models\User;
use App\Models\Wristband;
use App\Support\PosterImage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
        $paidRevenue = Payment::where('payment_status', 'success')->sum('total_amount');
        $scanTotal = ScanHistory::count();
        $scanSuccess = ScanHistory::where('scan_result', 'berhasil')->count();
        $insights = [
            'Pendapatan terverifikasi' => 'Rp'.number_format($paidRevenue, 0, ',', '.'),
            'Pembayaran pending' => Payment::where('payment_status', 'pending')->count(),
            'Order belum selesai' => Order::whereIn('order_status', ['pending', 'expired'])->count(),
            'Rasio scan berhasil' => $scanTotal > 0 ? round(($scanSuccess / $scanTotal) * 100).'%' : '0%',
        ];
        $orders = Order::with('user', 'concert', 'ticketZone', 'payment')->latest()->limit(6)->get();

        return view('admin.dashboard', compact('stats', 'orders', 'insights'));
    }

    public function concerts()
    {
        $concerts = Concert::latest()->paginate(10);

        return view('admin.concerts', compact('concerts'));
    }

    public function editBanner()
    {
        return view('admin.banner', [
            'settings' => SiteSetting::homeBannerSettings(),
            'concerts' => Concert::where('status', 'aktif')->latest('date')->get(),
        ]);
    }

    public function updateBanner(Request $request)
    {
        $settings = SiteSetting::homeBannerSettings();
        $rules = [];

        foreach ($settings['slots'] as $position => $slot) {
            $rules["slots.{$position}.source"] = ['required', 'in:auto,custom,concert'];
            $rules["slots.{$position}.concert_id"] = ['nullable', 'required_if:slots.'.$position.'.source,concert', 'exists:concerts,id'];
            $rules["slots.{$position}.banner"] = ['nullable', 'image', 'dimensions:ratio=4/1', 'max:10240'];
            $rules["slots.{$position}.title"] = ['nullable', 'string', 'max:150'];
            $rules["slots.{$position}.link"] = ['nullable', 'string', 'max:2048'];

            if ($request->input("slots.{$position}.source") === 'custom' && ! $request->hasFile("slots.{$position}.banner") && blank($slot['image'])) {
                $rules["slots.{$position}.banner"][] = 'required';
            }
        }

        $data = $request->validate($rules);

        foreach ($settings['slots'] as $position => $slot) {
            $slotData = $data['slots'][$position];
            $imagePath = $slot['image'];

            if ($request->hasFile("slots.{$position}.banner")) {
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }

                $imagePath = PosterImage::storeWebp($request->file("slots.{$position}.banner"), 'banners');
            }

            SiteSetting::putMany([
                "home_banner_{$position}_source" => $slotData['source'],
                "home_banner_{$position}_concert_id" => $slotData['source'] === 'concert' ? (string) $slotData['concert_id'] : null,
                "home_banner_{$position}_image" => $imagePath,
                "home_banner_{$position}_title" => $slotData['title'] ?? null,
                "home_banner_{$position}_link" => $slotData['link'] ?? null,
            ]);
        }

        return back()->with('success', 'Banner utama diperbarui.');
    }

    public function createConcert()
    {
        return view('admin.concert-create');
    }

    public function storeConcert(Request $request)
    {
        $concert = Concert::create($this->concertData($request) + ['admin_id' => Auth::guard('admin')->id()]);
        $this->syncDefaultZones($concert);

        return redirect()->route('admin.concerts')->with('success', 'Konser ditambahkan.');
    }

    public function editConcert(Concert $concert)
    {
        $concert->load('ticketZones');

        return view('admin.concert-edit', compact('concert'));
    }

    public function updateConcert(Request $request, Concert $concert)
    {
        $concert->update($this->concertData($request, $concert));
        $this->syncDefaultZones($concert);

        return redirect()->route('admin.concerts.edit', $concert)->with('success', 'Konser diperbarui.');
    }

    public function destroyConcert(Concert $concert)
    {
        if ($concert->poster) {
            Storage::disk('public')->delete($concert->poster);
        }

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
            'paymentStats' => [
                'pending' => Payment::where('payment_status', 'pending')->count(),
                'success' => Payment::where('payment_status', 'success')->count(),
                'failed' => Payment::where('payment_status', 'failed')->count(),
            ],
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
        $payment = Payment::create($data + ['total_amount' => $order->total_price, 'payment_date' => now()]);

        $this->syncOrderAfterPayment($payment);

        return back()->with('success', 'Pembayaran ditambahkan.');
    }

    public function updatePayment(Request $request, Payment $payment)
    {
        $data = $request->validate(['payment_status' => ['required', 'in:pending,success,failed']]);
        $payment->update($data + ['payment_date' => now()]);

        if ($data['payment_status'] === 'success') {
            $this->issueTickets($payment->order);
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

    public function reports(Request $request)
    {
        $histories = $this->scanHistoryQuery($request)
            ->latest('scanned_at')
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'total' => $this->scanHistoryQuery($request)->count(),
            'berhasil' => $this->scanHistoryQuery($request)->where('scan_result', 'berhasil')->count(),
            'gagal' => $this->scanHistoryQuery($request)->where('scan_result', 'gagal')->count(),
        ];

        return view('admin.reports', [
            'histories' => $histories,
            'summary' => $summary,
            'officers' => Officer::orderBy('name')->get(),
            'filters' => $request->only(['from', 'to', 'scan_type', 'scan_result', 'officer_id']),
        ]);
    }

    public function exportReports(Request $request)
    {
        $format = $request->query('format', 'csv');
        $histories = $this->scanHistoryQuery($request)->latest('scanned_at')->get();
        $fileStamp = now()->format('Ymd-His');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('pdf.scan-report', [
                'histories' => $histories,
                'filters' => $request->only(['from', 'to', 'scan_type', 'scan_result', 'officer_id']),
            ])->setPaper('a4', 'landscape');

            return $pdf->download("festify-scan-report-{$fileStamp}.pdf");
        }

        if ($format === 'xls') {
            $fileName = "festify-scan-report-{$fileStamp}.xls";

            return response()->streamDownload(function () use ($histories) {
                echo '<table border="1">';
                echo '<tr><th>Waktu</th><th>Petugas</th><th>Tipe</th><th>Hasil</th><th>Kode E-Ticket</th><th>Kode Gelang</th><th>User</th><th>Pesan</th></tr>';
                foreach ($histories as $history) {
                    echo '<tr>';
                    echo '<td>'.e($history->scanned_at?->format('Y-m-d H:i:s')).'</td>';
                    echo '<td>'.e($history->officer?->name).'</td>';
                    echo '<td>'.e($history->scan_type).'</td>';
                    echo '<td>'.e($history->scan_result).'</td>';
                    echo '<td>'.e($history->eTicket?->ticket_code).'</td>';
                    echo '<td>'.e($history->wristband?->wristband_code).'</td>';
                    echo '<td>'.e($history->eTicket?->user?->name ?? $history->wristband?->eTicket?->user?->name).'</td>';
                    echo '<td>'.e($history->message).'</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }, $fileName, ['Content-Type' => 'application/vnd.ms-excel']);
        }

        $fileName = "festify-scan-report-{$fileStamp}.csv";

        return response()->streamDownload(function () use ($histories) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Waktu', 'Petugas', 'Tipe', 'Hasil', 'Kode E-Ticket', 'Kode Gelang', 'User', 'Pesan']);

            foreach ($histories as $history) {
                fputcsv($output, [
                    $history->scanned_at?->format('Y-m-d H:i:s'),
                    $history->officer?->name,
                    $history->scan_type,
                    $history->scan_result,
                    $history->eTicket?->ticket_code,
                    $history->wristband?->wristband_code,
                    $history->eTicket?->user?->name ?? $history->wristband?->eTicket?->user?->name,
                    $history->message,
                ]);
            }

            fclose($output);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    public function destroyScanHistory(ScanHistory $history)
    {
        $history->delete();

        return back()->with('success', 'Riwayat scan dihapus.');
    }

    private function syncOrderAfterPayment(Payment $payment): void
    {
        if ($payment->payment_status === 'success') {
            $this->issueTickets($payment->order);

            return;
        }

        if ($payment->payment_status === 'failed') {
            $payment->order->update(['order_status' => 'cancelled']);

            return;
        }

        $payment->order->update(['order_status' => 'pending']);
    }

    private function scanHistoryQuery(Request $request)
    {
        return ScanHistory::with('officer', 'eTicket.user', 'wristband.eTicket.user')
            ->when($request->filled('from'), fn ($query) => $query->whereDate('scanned_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($query) => $query->whereDate('scanned_at', '<=', $request->date('to')))
            ->when($request->filled('scan_type'), fn ($query) => $query->where('scan_type', $request->input('scan_type')))
            ->when($request->filled('scan_result'), fn ($query) => $query->where('scan_result', $request->input('scan_result')))
            ->when($request->filled('officer_id'), fn ($query) => $query->where('officer_id', $request->integer('officer_id')));
    }

    private function issueTickets(Order $order): void
    {
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
            'poster' => ['nullable', 'image', 'dimensions:ratio=4/1', 'max:10240'],
            'price' => ['required', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:aktif,selesai,dibatalkan'],
            'is_promo' => ['nullable', 'boolean'],
        ]);

        $data['seat_zone'] = 'Festival Tengah';
        $data['is_promo'] = $request->boolean('is_promo');

        if ($request->hasFile('poster')) {
            if ($concert?->poster) {
                Storage::disk('public')->delete($concert->poster);
            }
            $data['poster'] = PosterImage::storeWebp($request->file('poster'));
        } elseif ($concert) {
            unset($data['poster']);
        }

        return $data;
    }

    private function syncDefaultZones(Concert $concert): void
    {
        $frontStock = (int) floor($concert->stock * 0.3);
        $middleStock = (int) floor($concert->stock * 0.45);
        $backStock = max(0, $concert->stock - $frontStock - $middleStock);

        $zones = [
            ['name' => 'Festival Depan', 'price' => $concert->price + 10000, 'stock' => $frontStock, 'color' => '#dc2626', 'position' => 1],
            ['name' => 'Festival Tengah', 'price' => $concert->price + 5000, 'stock' => $middleStock, 'color' => '#ea580c', 'position' => 2],
            ['name' => 'Tribune Belakang', 'price' => $concert->price, 'stock' => $backStock, 'color' => '#ca8a04', 'position' => 3],
        ];

        foreach ($zones as $zone) {
            $concert->ticketZones()->updateOrCreate(['position' => $zone['position']], $zone);
        }
    }
}
