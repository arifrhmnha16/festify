@php
    $ticket = $wristband->eTicket;
    $logo = \App\Support\PdfImage::containDataUri(public_path('logofest.png'), 184, 52, 62);
    $posterPath = $wristband->concert->poster ? public_path('storage/'.$wristband->concert->poster) : null;
    $poster = \App\Support\PdfImage::dataUri($posterPath, 210, 118, 42);
    $qr = \App\Support\PdfQr::dataUri($wristband->wristband_code, 4, 2);
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; size: 254mm 51mm; }
        body { margin: 0; font-family: Helvetica, Arial, sans-serif; color: #111; }
        .band { width: 720px; height: 144px; border: 1.3px solid #111; border-radius: 5px; overflow: hidden; }
        table { border-collapse: collapse; width: 100%; height: 100%; table-layout: fixed; }
        td { vertical-align: middle; }
        .side { width: 40px; background: #050505; color: #fff; text-align: center; }
        .side-orange { background: #d92d00; }
        .rotate { transform: rotate(-90deg); white-space: nowrap; font-size: 7px; font-weight: 900; letter-spacing: 3px; text-transform: uppercase; }
        .poster-cell { width: 124px; background: #171717; border-right: 1px dashed #ccc; padding: 8px; }
        .poster-box { height: 126px; background: #050505; border-radius: 5px; text-align: center; overflow: hidden; }
        .poster-box img { width: 100%; height: 100%; object-fit: contain; }
        .info { width: 204px; border-right: 1px dashed #ccc; padding: 9px 12px; }
        .logo { width: 92px; height: 26px; object-fit: contain; }
        .label { margin-top: 6px; color: #c2410c; font-size: 7px; font-weight: 900; letter-spacing: 1.7px; text-transform: uppercase; }
        h1 { margin: 4px 0 0; font-size: 14px; line-height: 1.06; font-weight: 900; }
        .meta { margin-top: 4px; color: #555; font-size: 8px; white-space: nowrap; overflow: hidden; }
        .mini { margin-top: 6px; }
        .mini div { display: inline-block; width: 64px; border: 1px solid #ddd; border-radius: 4px; padding: 4px; font-size: 7px; }
        .mini strong { display: block; margin-top: 1px; font-size: 7.5px; white-space: nowrap; overflow: hidden; }
        .qr-cell { width: 110px; border-right: 1px dashed #ccc; background: #fafaf9; text-align: center; }
        .qr-box { display: inline-block; background: white; padding: 6px; border-radius: 5px; }
        .qr-box img { width: 78px; height: 78px; }
        .qr-code { margin-top: 4px; font-size: 7px; font-family: Courier, monospace; font-weight: 900; }
        .holder { padding: 10px 12px; }
        .holder-label { color: #c2410c; font-size: 7px; font-weight: 900; letter-spacing: 2.5px; text-transform: uppercase; }
        .holder h2 { margin: 7px 0 0; font-size: 15px; font-weight: 900; white-space: nowrap; overflow: hidden; }
        .ticket-code { margin-top: 7px; font-size: 8px; color: #555; font-family: Courier, monospace; }
        .gate { margin-top: 12px; background: #050505; color: white; border-radius: 6px; padding: 8px; }
        .gate small { color: #fed7aa; text-transform: uppercase; letter-spacing: 1.7px; font-size: 7px; }
        .gate p { margin: 3px 0 0; font-size: 8px; font-weight: 800; line-height: 1.25; }
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
