@extends('layouts.app', ['title' => 'Gelang Saya'])
@section('content')
<section class="mx-auto max-w-7xl px-4 py-12">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-sm font-bold uppercase text-orange-700">Akses venue</p>
            <h1 class="mt-2 text-4xl font-black">Gelang Saya</h1>
            <p class="mt-3 text-neutral-600">Gelang akan muncul setelah E-Ticket ditukar oleh petugas loket di venue.</p>
        </div>
        <a href="{{ route('user.tickets') }}" class="rounded-full border px-5 py-3 font-bold">Lihat E-Ticket</a>
    </div>

    @if($wristbands->isNotEmpty())
        <div class="mt-8 grid gap-4 md:grid-cols-2">
            @foreach($wristbands as $wristband)
                <article class="rounded-lg border bg-white p-5 shadow-sm">
                    <p class="font-mono text-sm text-neutral-500">{{ $wristband->wristband_code }}</p>
                    <h2 class="mt-2 text-2xl font-black">{{ $wristband->concert->name }}</h2>
                    <p class="mt-1 text-neutral-600">{{ $wristband->eTicket->concert->venue }} - {{ $wristband->eTicket->concert->date->format('d M Y') }}</p>
                    <div class="mt-4 grid gap-3 rounded-lg bg-neutral-50 p-4 text-sm md:grid-cols-2">
                        <div>
                            <p class="text-neutral-500">Area</p>
                            <p class="font-bold">{{ $wristband->eTicket->order->ticketZone?->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-500">Status</p>
                            <p class="font-bold">{{ $wristband->wristband_status }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-6">{{ $wristbands->links() }}</div>
    @endif

    @if($pendingTickets->isNotEmpty())
        <div class="mt-8 rounded-lg border border-amber-200 bg-amber-50 p-5 text-amber-900">
            <h2 class="text-lg font-black">Tukarkan tiket Anda terlebih dahulu</h2>
            <p class="mt-2 text-sm">Beberapa E-Ticket belum memiliki gelang. Datang ke loket venue dan tunjukkan QR E-Ticket berikut.</p>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                @foreach($pendingTickets as $ticket)
                    <a href="{{ route('user.tickets.show', $ticket) }}" class="rounded-lg border border-amber-200 bg-white p-4">
                        <p class="font-mono text-sm">{{ $ticket->ticket_code }}</p>
                        <p class="mt-1 font-black">{{ $ticket->concert->name }}</p>
                        <p class="text-sm">{{ $ticket->order->ticketZone?->name ?? '-' }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if($wristbands->isEmpty() && $pendingTickets->isEmpty())
        <div class="mt-8 rounded-lg border bg-white p-8 text-center shadow-sm">
            <h2 class="text-2xl font-black">Belum ada gelang</h2>
            <p class="mt-2 text-neutral-600">Gelang akan tersedia setelah kamu membeli tiket, pembayaran diverifikasi, lalu E-Ticket ditukar di loket.</p>
            <a href="{{ route('user.concerts') }}" class="mt-5 inline-flex rounded-full bg-neutral-950 px-5 py-3 font-bold text-white">Cari Konser</a>
        </div>
    @endif
</section>
@endsection
