@extends('layouts.app', ['title' => 'Dashboard Loket'])
@section('content')
<section class="mx-auto max-w-6xl px-4 py-8 sm:py-12">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm font-bold uppercase text-orange-700">Petugas loket</p>
            <h1 class="mt-2 text-3xl font-black sm:text-4xl">Dashboard Loket</h1>
        </div>
        <a class="rounded-full bg-neutral-950 px-6 py-3 text-sm font-bold text-white" href="{{ route('loket.scan') }}">Scan QR E-Ticket</a>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border bg-white p-6">
            <p>E-Ticket sudah ditukar</p>
            <strong class="text-4xl">{{ $exchanged }}</strong>
        </div>
        <div class="rounded-lg border bg-white p-6">
            <p>Gelang aktif</p>
            <strong class="text-4xl">{{ $active }}</strong>
        </div>
        <div class="rounded-lg border bg-white p-6">
            <p>Scan hari ini</p>
            <strong class="text-4xl">{{ $todayScans }}</strong>
        </div>
        <div class="rounded-lg border border-red-200 bg-red-50 p-6 text-red-800">
            <p>Gagal hari ini</p>
            <strong class="text-4xl">{{ $failedToday }}</strong>
        </div>
    </div>

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
                            <td class="p-3"><span class="{{ $history->scan_result === 'berhasil' ? 'fi-badge-success' : 'fi-badge-danger' }}">{{ $history->scan_result }}</span></td>
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
