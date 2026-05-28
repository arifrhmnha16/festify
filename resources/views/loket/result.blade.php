@extends('layouts.app', ['title' => 'Hasil Loket'])
@section('content')
<style>
    @media print {
        @page { size: landscape; margin: 8mm; }
        body { background: white !important; }
        body * { visibility: hidden; }
        #wristband-print, #wristband-print * { visibility: visible; }
        #wristband-print {
            position: fixed;
            inset: 0;
            width: 100%;
            margin: 0;
            box-shadow: none !important;
            border: 1px solid #111 !important;
        }
        .no-print { display: none !important; }
    }
</style>

<section class="mx-auto max-w-6xl px-4 py-12">
    <div class="rounded-lg border bg-white p-8">
        <p class="text-sm font-bold uppercase {{ $success ? 'text-green-700' : 'text-red-700' }}">{{ $success ? 'Berhasil' : 'Ditolak' }}</p>
        <h1 class="mt-2 text-4xl font-black">{{ $message }}</h1>

        @if($ticket)
            <div class="mt-4 grid gap-2 text-sm text-neutral-600 md:grid-cols-2">
                <p>Konser: <strong class="text-neutral-950">{{ $ticket->concert->name }}</strong></p>
                <p>Pemilik: <strong class="text-neutral-950">{{ $ticket->user->name }}</strong></p>
                <p>E-Ticket: <strong class="font-mono text-neutral-950">{{ $ticket->ticket_code }}</strong></p>
                <p>Area: <strong class="text-neutral-950">{{ $ticket->order->ticketZone?->name ?? '-' }}</strong></p>
            </div>
        @endif

        @if($success && $ticket?->wristband)
            <div id="wristband-print" class="mt-8 overflow-hidden rounded-lg border-2 border-neutral-950 bg-white shadow-sm">
                <div class="grid min-h-[220px] grid-cols-[90px_180px_1fr_160px_1fr_90px] items-stretch">
                    <div class="flex items-center justify-center bg-neutral-950 text-white">
                        <div class="-rotate-90 whitespace-nowrap text-xs font-black uppercase tracking-[0.35em]">Festify Access</div>
                    </div>

                    <div class="border-r border-dashed border-neutral-300 bg-neutral-900 p-3">
                        <div class="grid h-full place-items-center overflow-hidden rounded-md bg-neutral-950">
                            @if($ticket->concert->poster)
                                <img src="{{ asset('storage/'.$ticket->concert->poster) }}" alt="{{ $ticket->concert->name }}" class="max-h-full max-w-full object-contain">
                            @else
                                <div class="flex h-full items-end bg-[linear-gradient(135deg,#111,#3f2a20)] p-3 text-white">
                                    <p class="text-xs font-black">{{ $ticket->concert->artist }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col justify-between border-r border-dashed border-neutral-300 p-5">
                        <div>
                            <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-10 w-36 rounded-md object-cover object-center">
                            <p class="mt-3 text-xs font-black uppercase tracking-widest text-orange-700">Wristband</p>
                            <h2 class="mt-2 text-3xl font-black leading-tight">{{ $ticket->concert->name }}</h2>
                            <p class="mt-2 text-sm text-neutral-600">{{ $ticket->concert->venue }} - {{ $ticket->concert->date->format('d M Y') }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="rounded border border-neutral-200 p-2">
                                <p class="text-neutral-500">Area</p>
                                <p class="font-black">{{ $ticket->order->ticketZone?->name ?? '-' }}</p>
                            </div>
                            <div class="rounded border border-neutral-200 p-2">
                                <p class="text-neutral-500">Status</p>
                                <p class="font-black">{{ $ticket->wristband->wristband_status }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center gap-3 border-r border-dashed border-neutral-300 bg-stone-50 p-4">
                        <div class="rounded bg-white p-3 shadow-sm">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(118)->generate($ticket->wristband->wristband_code) !!}
                        </div>
                        <p class="font-mono text-xs font-bold">{{ $ticket->wristband->wristband_code }}</p>
                    </div>

                    <div class="flex flex-col justify-between p-5">
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-orange-700">Holder</p>
                            <h3 class="mt-2 text-2xl font-black">{{ $ticket->user->name }}</h3>
                            <p class="mt-2 font-mono text-sm text-neutral-600">{{ $ticket->ticket_code }}</p>
                        </div>
                        <div class="rounded-lg bg-neutral-950 p-4 text-white">
                            <p class="text-xs uppercase tracking-widest text-orange-200">Gate validation</p>
                            <p class="mt-1 text-sm font-bold">Scan QR gelang satu kali di gate masuk.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-center bg-orange-700 text-white">
                        <div class="-rotate-90 whitespace-nowrap text-xs font-black uppercase tracking-[0.35em]">Valid Entry</div>
                    </div>
                </div>
            </div>

            <div class="no-print mt-6 flex flex-wrap gap-3">
                <button onclick="window.print()" class="rounded-full bg-neutral-950 px-6 py-3 font-bold text-white">Cetak Gelang</button>
                <a class="rounded-full border px-6 py-3 font-bold" href="{{ route('loket.scan') }}">Scan Lagi</a>
            </div>
        @else
            <a class="no-print mt-6 inline-flex rounded-full border px-5 py-3 font-bold" href="{{ route('loket.scan') }}">Scan Lagi</a>
        @endif
    </div>
</section>
@endsection
