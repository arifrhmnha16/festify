<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #171717; font-size: 11px; }
        h1 { margin: 0 0 4px; font-size: 22px; }
        p { margin: 0; color: #525252; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th { background: #171717; color: #fff; text-align: left; }
        th, td { border: 1px solid #d4d4d4; padding: 7px; vertical-align: top; }
        .meta { margin-top: 8px; }
        .code { font-family: DejaVu Sans Mono, monospace; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Laporan Riwayat Scan Festify</h1>
    <p>Dicetak: {{ now()->format('d M Y H:i') }}</p>
    <p class="meta">
        Filter:
        Dari {{ $filters['from'] ?? '-' }},
        Sampai {{ $filters['to'] ?? '-' }},
        Tipe {{ $filters['scan_type'] ?? 'semua' }},
        Hasil {{ $filters['scan_result'] ?? 'semua' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Petugas</th>
                <th>Tipe</th>
                <th>Hasil</th>
                <th>Kode</th>
                <th>User</th>
                <th>Pesan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $history)
                <tr>
                    <td>{{ $history->scanned_at?->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $history->officer?->name }}</td>
                    <td>{{ $history->scan_type }}</td>
                    <td>{{ $history->scan_result }}</td>
                    <td class="code">{{ $history->eTicket?->ticket_code ?? $history->wristband?->wristband_code ?? '-' }}</td>
                    <td>{{ $history->eTicket?->user?->name ?? $history->wristband?->eTicket?->user?->name ?? '-' }}</td>
                    <td>{{ $history->message }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Belum ada riwayat scan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
