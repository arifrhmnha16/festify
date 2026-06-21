@extends('layouts.app', ['title' => $ticket->ticket_code])
@section('content')
<style>
    @media print {
        @page { size: A4 portrait; margin: 10mm; }
        body { background: white !important; }
        body * { visibility: hidden; }
        #ticket-print, #ticket-print * { visibility: visible; }
        #ticket-print {
            position: fixed;
            inset: 0;
            width: 100%;
            margin: 0 !important;
            box-shadow: none !important;
            border: 1px solid #d4d4d4 !important;
        }
        .print-hidden { display: none !important; }
    }
</style>
<section class="mx-auto max-w-3xl px-4 py-12 print:p-0">
    <div id="ticket-print" class="rounded-lg border bg-white p-8 shadow-sm">
        <div class="grid gap-6">
            <div>
                <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-12 w-44 rounded-md object-cover object-center"><p class="mt-4 text-sm font-bold uppercase text-orange-700">E-Ticket</p><h1 class="mt-2 text-4xl font-black">{{ $ticket->concert->name }}</h1><p class="mt-2">{{ $ticket->user->name }} - {{ $ticket->concert->venue }}</p>
            </div>
            <div class="aspect-[4/1] overflow-hidden rounded-lg bg-neutral-950">
                @if($ticket->concert->poster)
                    <img src="{{ asset('storage/'.$ticket->concert->poster) }}" alt="{{ $ticket->concert->name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full items-end bg-[linear-gradient(135deg,#111,#3f2a20)] p-4 text-white">
                        <p class="text-sm font-black">{{ $ticket->concert->artist }}</p>
                    </div>
                @endif
            </div>
        </div>
        <div class="mt-6 grid gap-6 md:grid-cols-[180px_1fr]"><div class="rounded border p-4">{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->generate($ticket->ticket_code) !!}</div><div><p class="font-mono text-lg">{{ $ticket->ticket_code }}</p><p>Tanggal: {{ $ticket->concert->date->format('d M Y') }}</p><p>Area venue: <strong>{{ $ticket->order->ticketZone?->name ?? '-' }}</strong></p><p>Status E-Ticket: <strong>{{ $ticket->ticket_status }}</strong></p><p>Status gelang: <strong>{{ $ticket->wristband?->wristband_status ?? '-' }}</strong></p><p class="mt-4 text-sm text-neutral-600">Tukarkan E-Ticket ini di loket untuk mendapatkan gelang.</p></div></div>
        <div class="print-hidden mt-6">
            <x-venue-map :zones="$ticket->concert->ticketZones" :selected-zone-id="$ticket->order->ticket_zone_id" />
        </div>
        <button onclick="window.print()" class="print-hidden mt-6 rounded-full bg-neutral-950 px-5 py-3 font-bold text-white">Download / Print</button>
    </div>
</section>
@endsection
