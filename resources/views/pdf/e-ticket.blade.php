@php
    $logo = \App\Support\PdfImage::containDataUri(public_path('logofest.png'), 280, 78, 62);
    $posterPath = $ticket->concert->poster ? public_path('storage/'.$ticket->concert->poster) : null;
    $poster = \App\Support\PdfImage::dataUri($posterPath, 860, 220, 45);
    $qr = \App\Support\PdfQr::dataUri($ticket->ticket_code, 5, 2);
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 24px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #111; font-size: 11px; }
        .ticket { border: 1px solid #d9d9d9; border-radius: 10px; overflow: hidden; }
        .header { padding: 18px 22px; border-bottom: 1px solid #eee; }
        .logo { width: 140px; height: 39px; object-fit: contain; }
        .label { margin-top: 16px; color: #c2410c; font-size: 9px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; }
        h1 { margin: 6px 0 0; font-size: 24px; line-height: 1.12; font-weight: 900; }
        .muted { color: #555; }
        .code-box { float: right; margin-top: -38px; background: #111; color: white; padding: 9px 12px; border-radius: 8px; text-align: right; }
        .code-box small { color: #fed7aa; display: block; font-size: 8px; letter-spacing: 1.7px; text-transform: uppercase; }
        .poster { margin: 18px 22px; height: 118px; background: #111; border-radius: 9px; overflow: hidden; }
        .poster img { width: 100%; height: 100%; object-fit: cover; }
        .content { padding: 0 22px 20px; }
        .qr { width: 145px; padding: 10px; border: 1px solid #ddd; border-radius: 9px; text-align: center; vertical-align: top; }
        .qr img { width: 128px; height: 128px; }
        .details { padding-left: 22px; vertical-align: top; line-height: 1.85; }
        .note { margin-top: 12px; background: #fff7ed; color: #7c2d12; padding: 10px 12px; border-radius: 9px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            @if($logo)<img class="logo" src="{{ $logo }}" alt="Festify">@endif
            <div class="code-box">
                <small>Kode Tiket</small>
                <strong>{{ $ticket->ticket_code }}</strong>
            </div>
            <div class="label">E-Ticket</div>
            <h1>{{ $ticket->concert->name }}</h1>
            <p class="muted">{{ $ticket->user->name }} - {{ $ticket->concert->venue }}</p>
        </div>
        <div class="poster">
            @if($poster)<img src="{{ $poster }}" alt="{{ $ticket->concert->name }}">@endif
        </div>
        <table class="content" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="qr"><img src="{{ $qr }}" alt="QR {{ $ticket->ticket_code }}"></td>
                <td class="details">
                    <div><span class="muted">Tanggal:</span> <strong>{{ $ticket->concert->date->format('d M Y') }}</strong></div>
                    <div><span class="muted">Jam:</span> <strong>{{ substr($ticket->concert->time, 0, 5) }} WIB</strong></div>
                    <div><span class="muted">Area venue:</span> <strong>{{ $ticket->order->ticketZone?->name ?? '-' }}</strong></div>
                    <div><span class="muted">Status E-Ticket:</span> <strong>{{ $ticket->ticket_status }}</strong></div>
                    <div><span class="muted">Status gelang:</span> <strong>{{ $ticket->wristband?->wristband_status ?? '-' }}</strong></div>
                    <div class="note">Tukarkan E-Ticket ini di loket untuk mendapatkan gelang.</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
