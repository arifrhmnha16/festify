<?php

namespace App\Http\Controllers;

use App\Models\ETicket;
use App\Models\ScanHistory;
use App\Models\Wristband;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OfficerController extends Controller
{
    public function loketDashboard()
    {
        $exchanged = ETicket::where('ticket_status', 'sudah_ditukar')->count();
        $active = Wristband::where('wristband_status', 'aktif')->count();
        return view('loket.dashboard', compact('exchanged', 'active'));
    }

    public function scanEticket() { return view('loket.scan'); }

    public function exchange(Request $request, ?string $ticket_code = null)
    {
        $code = $ticket_code ?: $request->validate(['ticket_code' => ['required', 'string']])['ticket_code'];
        $ticket = ETicket::with('order.payment', 'order.ticketZone', 'concert', 'user', 'wristband')->where('ticket_code', $code)->first();
        $message = 'E-Ticket tidak ditemukan.';
        $success = false;

        if ($ticket && $ticket->order->payment?->payment_status !== 'success') {
            $message = 'Pembayaran belum berhasil.';
        } elseif ($ticket && $ticket->ticket_status !== 'belum_ditukar') {
            $message = 'E-Ticket sudah pernah ditukar atau invalid.';
        } elseif ($ticket && $ticket->concert->date->toDateString() !== now()->toDateString()) {
            $message = 'Konser tidak sesuai hari ini.';
        } elseif ($ticket) {
            $wristbandCode = 'GLG-'.now()->format('ymd').'-'.Str::upper(Str::random(8));
            $ticket->update(['ticket_status' => 'sudah_ditukar', 'exchanged_at' => now()]);
            $ticket->wristband()->create([
                'concert_id' => $ticket->concert_id,
                'wristband_code' => $wristbandCode,
                'wristband_qr_code' => $wristbandCode,
                'activated_at' => now(),
            ]);
            $ticket->load('order.ticketZone', 'wristband');
            $message = 'Penukaran berhasil. Gelang aktif.';
            $success = true;
        }

        $this->history('scan_eticket', $success, $message, $ticket?->id, null);
        return view('loket.result', compact('ticket', 'message', 'success'));
    }

    public function gateDashboard()
    {
        $entered = Wristband::where('wristband_status', 'sudah_masuk')->count();
        $active = Wristband::where('wristband_status', 'aktif')->count();
        return view('gate.dashboard', compact('entered', 'active'));
    }

    public function scanWristband() { return view('gate.scan'); }

    public function validateWristband(Request $request, ?string $wristband_code = null)
    {
        $code = $wristband_code ?: $request->validate(['wristband_code' => ['required', 'string']])['wristband_code'];
        $wristband = Wristband::with('eTicket.user', 'concert')->where('wristband_code', $code)->first();
        $message = 'Gelang tidak ditemukan.';
        $success = false;

        if ($wristband && $wristband->wristband_status !== 'aktif') {
            $message = 'Gelang sudah digunakan atau invalid.';
        } elseif ($wristband) {
            $wristband->update(['wristband_status' => 'sudah_masuk', 'entered_at' => now()]);
            $message = 'Akses diterima. Silakan masuk.';
            $success = true;
        }

        $this->history('scan_gelang', $success, $message, null, $wristband?->id);
        return view('gate.result', compact('wristband', 'message', 'success'));
    }

    private function history(string $type, bool $success, string $message, ?int $ticketId, ?int $wristbandId): void
    {
        ScanHistory::create([
            'officer_id' => Auth::guard('officer')->id(),
            'e_ticket_id' => $ticketId,
            'wristband_id' => $wristbandId,
            'scan_type' => $type,
            'scan_result' => $success ? 'berhasil' : 'gagal',
            'message' => $message,
            'scanned_at' => now(),
        ]);
    }
}
