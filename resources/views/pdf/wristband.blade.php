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
        html, body { width: 720pt; height: 144pt; margin: 0; padding: 0; overflow: hidden; }
        body { font-family: Helvetica, Arial, sans-serif; color: #111; }
        .band { width: 716pt; height: 140pt; margin: 2pt; border: 1pt solid #111; border-radius: 5pt; overflow: hidden; page-break-inside: avoid; }
        table { border-collapse: collapse; width: 100%; height: 100%; table-layout: fixed; }
        td { vertical-align: middle; height: 138pt; overflow: hidden; }
        .side { width: 36pt; background: #050505; color: #fff; text-align: center; }
        .side-orange { background: #d92d00; }
        .rotate { transform: rotate(-90deg); white-space: nowrap; font-size: 6.2pt; font-weight: 900; letter-spacing: 2.2pt; text-transform: uppercase; }
        .poster-cell { width: 118pt; background: #171717; border-right: 0.7pt dashed #ccc; padding: 7pt; }
        .poster-box { height: 124pt; background: #050505; border-radius: 5pt; text-align: center; overflow: hidden; }
        .poster-box img { width: 100%; height: 100%; object-fit: contain; }
        .info { width: 190pt; border-right: 0.7pt dashed #ccc; padding: 8pt 10pt; }
        .logo { width: 78pt; height: 22pt; object-fit: contain; }
        .label { margin-top: 6pt; color: #c2410c; font-size: 6.2pt; font-weight: 900; letter-spacing: 1.4pt; text-transform: uppercase; }
        h1 { margin: 4pt 0 0; font-size: 12.5pt; line-height: 1.03; font-weight: 900; max-height: 27pt; overflow: hidden; }
        .meta { margin-top: 4pt; color: #555; font-size: 6.7pt; white-space: nowrap; overflow: hidden; }
        .mini { margin-top: 7pt; white-space: nowrap; }
        .mini div { display: inline-block; width: 58pt; border: 0.7pt solid #ddd; border-radius: 4pt; padding: 4pt; font-size: 5.7pt; overflow: hidden; }
        .mini strong { display: block; margin-top: 2pt; font-size: 6.3pt; white-space: nowrap; overflow: hidden; }
        .qr-cell { width: 112pt; border-right: 0.7pt dashed #ccc; background: #fafaf9; text-align: center; }
        .qr-box { display: inline-block; background: white; padding: 6pt; border-radius: 5pt; }
        .qr-box img { width: 78pt; height: 78pt; }
        .qr-code { margin-top: 5pt; font-size: 6pt; font-family: Courier, monospace; font-weight: 900; }
        .holder { width: 220pt; padding: 10pt 12pt; }
        .holder-label { color: #c2410c; font-size: 6.2pt; font-weight: 900; letter-spacing: 2pt; text-transform: uppercase; }
        .holder h2 { margin: 8pt 0 0; font-size: 13pt; font-weight: 900; white-space: nowrap; overflow: hidden; }
        .ticket-code { margin-top: 8pt; font-size: 6.8pt; color: #555; font-family: Courier, monospace; }
        .gate { margin-top: 9pt; background: #050505; color: white; border-radius: 5pt; padding: 8pt; width: 150pt; }
        .gate small { color: #fed7aa; text-transform: uppercase; letter-spacing: 1.4pt; font-size: 5.8pt; }
        .gate p { margin: 4pt 0 0; font-size: 6.8pt; font-weight: 800; line-height: 1.18; }
    </style>
</head>
<body>
    <div class="band">
        <table>
            <tr>
                <td class="side"><div class="rotate">Festify Access</div></td>
                <td class="poster-cell">
                    <div class="poster-box">@if($poster)<img src="{{ $poster }}" alt="{{ $wristband->concert->name }}">@endif</div>
                </td>
                <td class="info">
                    @if($logo)<img class="logo" src="{{ $logo }}" alt="Festify">@endif
                    <div class="label">Wristband</div>
                    <h1>{{ $wristband->concert->name }}</h1>
                    <div class="meta">{{ $wristband->concert->venue }} - {{ $wristband->concert->date->format('d M Y') }}</div>
                    <div class="mini">
                        <div>Area<strong>{{ $ticket->order->ticketZone?->name ?? '-' }}</strong></div>
                        <div>Status<strong>{{ $wristband->wristband_status }}</strong></div>
                    </div>
                </td>
                <td class="qr-cell">
                    <div class="qr-box"><img src="{{ $qr }}" alt="QR {{ $wristband->wristband_code }}"></div>
                    <div class="qr-code">{{ $wristband->wristband_code }}</div>
                </td>
                <td class="holder">
                    <div class="holder-label">Holder</div>
                    <h2>{{ $ticket->user->name }}</h2>
                    <div class="ticket-code">{{ $ticket->ticket_code }}</div>
                    <div class="gate">
                        <small>Gate Validation</small>
                        <p>Scan QR gelang satu kali di gate masuk.</p>
                    </div>
                </td>
                <td class="side side-orange"><div class="rotate">Valid Entry</div></td>
            </tr>
        </table>
    </div>
</body>
</html>
