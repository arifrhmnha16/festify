@extends('layouts.app', ['title' => 'Dashboard Loket'])
@section('content')
<section class="mx-auto max-w-5xl px-4 py-12">
    <h1 class="text-4xl font-black">Dashboard Loket</h1>

    <div class="mt-8 grid gap-4 md:grid-cols-2">
        <div class="rounded-lg border bg-white p-6">
            <p>E-Ticket sudah ditukar</p>
            <strong class="text-4xl">{{ $exchanged }}</strong>
        </div>
        <div class="rounded-lg border bg-white p-6">
            <p>Gelang aktif</p>
            <strong class="text-4xl">{{ $active }}</strong>
        </div>
    </div>

    <a class="mt-8 inline-flex rounded-full bg-neutral-950 px-6 py-3 font-bold text-white" href="{{ route('loket.scan') }}">Scan QR E-Ticket</a>

    <div class="mt-8 overflow-hidden rounded-lg border bg-white">
        <div class="border-b px-5 py-4">
            <h2 class="text-lg font-black">Riwayat Scan Terakhir</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[720px] w-full text-left text-sm">
                <thead class="bg-neutral-100 text-xs uppercase text-neutral-600">
                    <tr>
                        <th class="p-3">Waktu</th>
                        <th class="p-3">Petugas</th>
                        <th class="p-3">User</th>
                        <th class="p-3">Hasil</th>
                        <th class="p-3">Pesan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                        <tr class="border-t">
                            <td class="p-3">{{ $history->scanned_at->format('d M Y H:i') }}</td>
                            <td class="p-3 font-bold">{{ $history->officer->name }}</td>
                            <td class="p-3">{{ $history->eTicket?->user?->name ?? '-' }}</td>
                            <td class="p-3">{{ $history->scan_result }}</td>
                            <td class="p-3">{{ $history->message }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-5 text-center text-neutral-500">Belum ada riwayat scan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
