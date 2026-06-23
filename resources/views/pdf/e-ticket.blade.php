@php
    $logo = \App\Support\PdfImage::containDataUri(public_path('logofest.png'), 280, 78, 62);
    $posterPath = null;
    if ($ticket->concert->poster) {
        $publicPoster = public_path('posters/'.basename($ticket->concert->poster));
        $storagePoster = public_path('storage/'.$ticket->concert->poster);
        $posterPath = is_file($publicPoster) ? $publicPoster : $storagePoster;
    }
    $poster = \App\Support\PdfImage::dataUri($posterPath, 860, 220, 45);
    $qr = \App\Support\PdfQr::dataUri($ticket->ticket_code, 5, 2);
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 14mm; size: A4 portrait; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Helvetica, Arial, sans-serif; color: #111; font-size: 11px; }
        .ticket { width: 100%; border: 1px solid #d9d9d9; border-radius: 10px; overflow: hidden; }
        .header { padding: 18px 22px; border-bottom: 1px solid #eee; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-left { vertical-align: top; }
        .header-right { width: 190px; text-align: right; vertical-align: top; }
        .logo { width: 140px; height: 39px; object-fit: contain; }
        .label { margin-top: 16px; color: #c2410c; font-size: 9px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; }
        h1 { margin: 6px 0 0; font-size: 24px; line-height: 1.12; font-weight: 900; }
        .muted { color: #555; }
        .code-box { display: inline-block; background: #111; color: white; padding: 10px 12px; border-radius: 8px; text-align: right; }
        .code-box small { color: #fed7aa; display: block; font-size: 8px; letter-spacing: 1.7px; text-transform: uppercase; }
        .code-box strong { font-family: Courier, monospace; font-size: 10px; }
        .poster { margin: 18px 22px; height: 44mm; background: #111; border-radius: 9px; overflow: hidden; }
        .poster img { width: 100%; height: 100%; object-fit: cover; }
        .content { padding: 0 22px 20px; }
        .qr { width: 42mm; padding: 10px; border: 1px solid #ddd; border-radius: 9px; text-align: center; vertical-align: top; }
        .qr img { width: 128px; height: 128px; }
        .details { padding-left: 22px; vertical-align: top; line-height: 1.85; }
        .note { margin-top: 12px; background: #fff7ed; color: #7c2d12; padding: 10px 12px; border-radius: 9px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="header-left">
                        @if($logo)<img class="logo" src="{{ $logo }}" alt="Festify">@endif
                        <div class="label">E-Ticket</div>
                        <h1>{{ $ticket->concert->name }}</h1>
                        <p class="muted">{{ $ticket->user->name }} - {{ $ticket->concert->venue }}</p>
                    </td>
                    <td class="header-right">
                        <div class="code-box">
                            <small>Kode Tiket</small>
                            <strong>{{ $ticket->ticket_code }}</strong>
                        </div>
                    </td>
                </tr>
            </table>
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
