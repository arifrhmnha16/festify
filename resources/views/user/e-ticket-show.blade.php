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
<section class="mx-auto max-w-3xl px-4 py-10 print:p-0">
    <div id="ticket-print" class="overflow-hidden rounded-lg border bg-white shadow-sm">
        <div class="border-b border-neutral-200 p-6 md:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-10 w-36 rounded-md object-cover object-center">
                    <p class="mt-4 text-xs font-bold uppercase tracking-widest text-orange-700">E-Ticket</p>
                    <h1 class="mt-2 text-3xl font-black leading-tight md:text-4xl">{{ $ticket->concert->name }}</h1>
                    <p class="mt-2 text-neutral-600">{{ $ticket->user->name }} - {{ $ticket->concert->venue }}</p>
                </div>
                <div class="rounded-lg bg-neutral-950 px-4 py-3 text-right text-white">
                    <p class="text-xs uppercase tracking-widest text-orange-200">Kode Tiket</p>
                    <p class="mt-1 font-mono text-sm font-black">{{ $ticket->ticket_code }}</p>
                </div>
            </div>
        </div>
        <div class="p-6 md:p-8">
            <div class="aspect-[4/1] overflow-hidden rounded-lg bg-neutral-950">
                <x-concert-poster :concert="$ticket->concert" class="h-full w-full object-cover" />
            </div>
            <div class="mt-6 grid gap-6 md:grid-cols-[176px_1fr]">
                <div class="grid place-items-center rounded-lg border p-4">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->generate($ticket->ticket_code) !!}
                </div>
                <div class="grid gap-3 text-sm">
                    <p><span class="text-neutral-500">Tanggal:</span> <strong>{{ $ticket->concert->date->format('d M Y') }}</strong></p>
                    <p><span class="text-neutral-500">Jam:</span> <strong>{{ substr($ticket->concert->time, 0, 5) }} WIB</strong></p>
                    <p><span class="text-neutral-500">Area venue:</span> <strong>{{ $ticket->order->ticketZone?->name ?? '-' }}</strong></p>
                    <p><span class="text-neutral-500">Status E-Ticket:</span> <strong>{{ $ticket->ticket_status }}</strong></p>
                    <p><span class="text-neutral-500">Status gelang:</span> <strong>{{ $ticket->wristband?->wristband_status ?? '-' }}</strong></p>
                    <p class="rounded-lg bg-orange-50 p-3 text-sm font-bold text-orange-900">Tukarkan E-Ticket ini di loket untuk mendapatkan gelang.</p>
                </div>
            </div>
        </div>
        <div class="pdf-hidden print-hidden border-t border-neutral-100 p-6 md:p-8">
            <x-venue-map :zones="$ticket->concert->ticketZones" :selected-zone-id="$ticket->order->ticket_zone_id" />
        </div>
        <div class="pdf-hidden print-hidden flex flex-wrap gap-3 border-t border-neutral-100 p-6 md:p-8">
            <a href="{{ route('user.tickets.download', $ticket) }}" class="rounded-full bg-neutral-950 px-5 py-3 font-bold text-white">Download PDF</a>
            <a href="{{ route('user.tickets.download', ['ticket' => $ticket, 'mode' => 'print']) }}" class="rounded-full border px-5 py-3 font-bold">Print Tiket</a>
        </div>
    </div>
</section>
@endsection
