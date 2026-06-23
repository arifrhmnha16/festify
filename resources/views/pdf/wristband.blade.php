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
        @page { margin: 0; size: 254mm 51mm; }
        * { box-sizing: border-box; }
        html, body { width: 254mm; height: 51mm; margin: 0; padding: 0; }
        body { font-family: Helvetica, Arial, sans-serif; color: #111; }
        .band { width: 254mm; height: 51mm; border: 1.2pt solid #111; border-radius: 2mm; overflow: hidden; }
        table { border-collapse: collapse; width: 100%; height: 100%; table-layout: fixed; }
        td { vertical-align: middle; }
        .side { width: 14mm; background: #050505; color: #fff; text-align: center; }
        .side-orange { background: #d92d00; }
        .rotate { transform: rotate(-90deg); white-space: nowrap; font-size: 7pt; font-weight: 900; letter-spacing: 2.7pt; text-transform: uppercase; }
        .poster-cell { width: 44mm; background: #171717; border-right: 0.8pt dashed #ccc; padding: 2.5mm; }
        .poster-box { height: 45mm; background: #050505; border-radius: 1.8mm; text-align: center; overflow: hidden; }
        .poster-box img { width: 100%; height: 100%; object-fit: contain; }
        .info { width: 72mm; border-right: 0.8pt dashed #ccc; padding: 3mm 4mm; }
        .logo { width: 33mm; height: 9mm; object-fit: contain; }
        .label { margin-top: 2mm; color: #c2410c; font-size: 7pt; font-weight: 900; letter-spacing: 1.5pt; text-transform: uppercase; }
        h1 { margin: 1.5mm 0 0; font-size: 14pt; line-height: 1.06; font-weight: 900; }
        .meta { margin-top: 1.2mm; color: #555; font-size: 7.5pt; white-space: nowrap; overflow: hidden; }
        .mini { margin-top: 2mm; }
        .mini div { display: inline-block; width: 23mm; border: 0.7pt solid #ddd; border-radius: 1.5mm; padding: 1.5mm; font-size: 6.5pt; }
        .mini strong { display: block; margin-top: 0.5mm; font-size: 7pt; white-space: nowrap; overflow: hidden; }
        .qr-cell { width: 39mm; border-right: 0.8pt dashed #ccc; background: #fafaf9; text-align: center; }
        .qr-box { display: inline-block; background: white; padding: 2mm; border-radius: 1.5mm; }
        .qr-box img { width: 27mm; height: 27mm; }
        .qr-code { margin-top: 1.5mm; font-size: 6.5pt; font-family: Courier, monospace; font-weight: 900; }
        .holder { width: 71mm; padding: 3.2mm 4mm; }
        .holder-label { color: #c2410c; font-size: 7pt; font-weight: 900; letter-spacing: 2.2pt; text-transform: uppercase; }
        .holder h2 { margin: 2.5mm 0 0; font-size: 14pt; font-weight: 900; white-space: nowrap; overflow: hidden; }
        .ticket-code { margin-top: 2mm; font-size: 7.5pt; color: #555; font-family: Courier, monospace; }
        .gate { margin-top: 3.5mm; background: #050505; color: white; border-radius: 2mm; padding: 2.6mm; }
        .gate small { color: #fed7aa; text-transform: uppercase; letter-spacing: 1.5pt; font-size: 6.5pt; }
        .gate p { margin: 1mm 0 0; font-size: 7.5pt; font-weight: 800; line-height: 1.25; }
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
