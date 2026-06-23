@php
    $ticket = $wristband->eTicket;
    $logo = \App\Support\PdfImage::containDataUri(public_path('logofest.png'), 184, 52, 62);
    $posterPath = null;
    if ($wristband->concert->poster) {
        $publicPoster = public_path('posters/'.basename($wristband->concert->poster));
        $storagePoster = public_path('storage/'.$wristband->concert->poster);
        $posterPath = is_file($publicPoster) ? $publicPoster : $storagePoster;
    }
    $poster = \App\Support\PdfImage::dataUri($posterPath, 210, 118, 42);
    $qr = \App\Support\PdfQr::dataUri($wristband->wristband_code, 4, 2);
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; size: 720pt 144pt; }
        * { box-sizing: border-box; }
        html, body { width: 720pt; height: 144pt; margin: 0; padding: 0; overflow: hidden; line-height: 1; }
        body { font-family: Helvetica, Arial, sans-serif; color: #111; }
        .band { position: relative; width: 718pt; height: 140pt; margin: 1pt; overflow: hidden; border: 1pt solid #111; border-radius: 5pt; page-break-after: avoid; page-break-before: avoid; page-break-inside: avoid; }
        .panel { position: absolute; top: 0; height: 140pt; overflow: hidden; }
        .side { left: 0; width: 40.5pt; background: #050505; color: #fff; text-align: center; }
        .side-orange { left: 677.5pt; background: #d92d00; }
        .vertical { padding-top: 34pt; font-size: 6.1pt; line-height: 6.8pt; font-weight: 900; letter-spacing: 1.4pt; text-transform: uppercase; }
        .poster-cell { left: 40.5pt; width: 126pt; background: #171717; border-right: 0.7pt dashed #ccc; }
        .poster-box { position: absolute; left: 20pt; top: 22pt; width: 86pt; height: 92pt; background: #050505; border-radius: 5pt; text-align: center; overflow: hidden; }
        .poster-box img { width: 100%; height: 100%; object-fit: contain; }
        .info { left: 166.5pt; width: 202.5pt; border-right: 0.7pt dashed #ccc; padding: 18pt 12pt 8pt 12pt; }
        .logo { width: 83pt; height: 24pt; object-fit: contain; }
        .label { margin-top: 6pt; color: #c2410c; font-size: 6.4pt; font-weight: 900; letter-spacing: 2.2pt; text-transform: uppercase; }
        h1 { margin: 5pt 0 0; font-size: 13pt; line-height: 1.08; font-weight: 900; max-height: 31pt; overflow: hidden; }
        .meta { margin-top: 5pt; color: #555; font-size: 6.5pt; white-space: nowrap; overflow: hidden; }
        .mini { position: absolute; left: 12pt; top: 103pt; white-space: nowrap; }
        .mini div { display: inline-block; width: 80pt; height: 24pt; border: 0.7pt solid #ddd; border-radius: 4pt; padding: 3pt 4pt; font-size: 5.8pt; overflow: hidden; }
        .mini strong { display: block; margin-top: 2pt; font-size: 6.5pt; white-space: nowrap; overflow: hidden; }
        .qr-cell { left: 369pt; width: 111pt; border-right: 0.7pt dashed #ccc; background: #fafaf9; text-align: center; padding-top: 19pt; }
        .qr-box { display: inline-block; background: white; padding: 6pt; border-radius: 5pt; }
        .qr-box img { width: 72pt; height: 72pt; }
        .qr-code { margin-top: 6pt; font-size: 6.2pt; font-family: Courier, monospace; font-weight: 900; }
        .holder { left: 480pt; width: 199.5pt; padding: 20pt 12pt 8pt 12pt; }
        .holder-label { color: #c2410c; font-size: 6.2pt; font-weight: 900; letter-spacing: 3pt; text-transform: uppercase; }
        .holder h2 { margin: 12pt 0 0; font-size: 13pt; font-weight: 900; white-space: nowrap; overflow: hidden; }
        .ticket-code { margin-top: 9pt; font-size: 6.5pt; color: #555; font-family: Courier, monospace; }
        .gate { position: absolute; left: 12pt; top: 90pt; background: #050505; color: white; border-radius: 5pt; padding: 7pt 8pt; width: 162pt; height: 34pt; }
        .gate small { color: #fed7aa; text-transform: uppercase; letter-spacing: 1.6pt; font-size: 5.8pt; }
        .gate p { margin: 4pt 0 0; font-size: 6.8pt; font-weight: 800; line-height: 1.1; }
    </style>
</head>
<body>
    <div class="band">
        <div class="panel side"><div class="vertical">F<br>E<br>S<br>T<br>I<br>F<br>Y<br>&nbsp;<br>A<br>C<br>C<br>E<br>S<br>S</div></div>
        <div class="panel poster-cell">
            <div class="poster-box">@if($poster)<img src="{{ $poster }}" alt="{{ $wristband->concert->name }}">@endif</div>
        </div>
        <div class="panel info">
            @if($logo)<img class="logo" src="{{ $logo }}" alt="Festify">@endif
            <div class="label">Wristband</div>
            <h1>{{ $wristband->concert->name }}</h1>
            <div class="meta">{{ $wristband->concert->venue }} - {{ $wristband->concert->date->format('d M Y') }}</div>
            <div class="mini">
                <div>Area<strong>{{ $ticket->order->ticketZone?->name ?? '-' }}</strong></div>
                <div>Status<strong>{{ $wristband->wristband_status }}</strong></div>
            </div>
        </div>
        <div class="panel qr-cell">
            <div class="qr-box"><img src="{{ $qr }}" alt="QR {{ $wristband->wristband_code }}"></div>
            <div class="qr-code">{{ $wristband->wristband_code }}</div>
        </div>
        <div class="panel holder">
            <div class="holder-label">Holder</div>
            <h2>{{ $ticket->user->name }}</h2>
            <div class="ticket-code">{{ $ticket->ticket_code }}</div>
            <div class="gate">
                <small>Gate Validation</small>
                <p>Scan QR gelang satu kali di gate masuk.</p>
            </div>
        </div>
        <div class="panel side side-orange"><div class="vertical">V<br>A<br>L<br>I<br>D<br>&nbsp;<br>E<br>N<br>T<br>R<br>Y</div></div>
    </div>
</body>
</html>
