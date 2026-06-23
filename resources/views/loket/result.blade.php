@extends('layouts.app', ['title' => 'Hasil Loket'])
@section('content')
<style>
    @media print {
        @page { size: 254mm 51mm; margin: 0; }
        html, body { width: 254mm; height: 51mm; margin: 0 !important; padding: 0 !important; overflow: hidden; background: white !important; }
        body * { visibility: hidden; }
        #wristband-print, #wristband-print * { visibility: visible; }
        #wristband-print {
            position: fixed;
            left: 0;
            top: 0;
            width: 254mm !important;
            height: 51mm !important;
            margin: 0;
            box-shadow: none !important;
            border: 1px solid #111 !important;
            border-radius: 0 !important;
        }
        #wristband-print > div { width: 100% !important; height: 100% !important; }
        .no-print { display: none !important; }
    }
</style>

<section class="mx-auto max-w-6xl px-4 py-10">
    <div class="rounded-lg border bg-white p-6 md:p-8">
        <p class="text-sm font-bold uppercase {{ $success ? 'text-green-700' : 'text-red-700' }}">{{ $success ? 'Berhasil' : 'Ditolak' }}</p>
        <h1 class="mt-2 max-w-5xl text-3xl font-black leading-tight md:text-4xl">{{ $message }}</h1>

        @if($ticket)
            <div class="mt-4 grid gap-2 text-sm text-neutral-600 md:grid-cols-2">
                <p>Konser: <strong class="text-neutral-950">{{ $ticket->concert->name }}</strong></p>
                <p>Pemilik: <strong class="text-neutral-950">{{ $ticket->user->name }}</strong></p>
                <p>E-Ticket: <strong class="font-mono text-neutral-950">{{ $ticket->ticket_code }}</strong></p>
                <p>Area: <strong class="text-neutral-950">{{ $ticket->order->ticketZone?->name ?? '-' }}</strong></p>
            </div>
        @endif

        @if($success && $ticket?->wristband)
            <div class="mt-8 overflow-x-auto pb-2">
            <div id="wristband-print" class="w-[960px] overflow-hidden rounded-lg border-2 border-neutral-950 bg-white shadow-sm">
                <div class="grid h-[192px] grid-cols-[54px_168px_270px_148px_266px_54px] items-stretch">
                    <div class="flex items-center justify-center bg-neutral-950 text-white">
                        <div class="-rotate-90 whitespace-nowrap text-[9px] font-black uppercase tracking-[0.32em]">Festify Access</div>
                    </div>

                    <div class="border-r border-dashed border-neutral-300 bg-neutral-900 p-2.5">
                        <div class="grid h-full place-items-center overflow-hidden rounded-md bg-neutral-950">
                            @if($ticket->concert->poster)
                                <img src="{{ $ticket->concert->poster_url }}" alt="{{ $ticket->concert->name }}" class="max-h-full max-w-full object-contain">
                            @else
                                <div class="flex h-full items-end bg-[linear-gradient(135deg,#111,#3f2a20)] p-3 text-white">
                                    <p class="text-xs font-black">{{ $ticket->concert->artist }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col justify-between border-r border-dashed border-neutral-300 p-4">
                        <div>
                            <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-8 w-28 rounded-md object-cover object-center">
                            <p class="mt-2 text-[9px] font-black uppercase tracking-[0.2em] text-orange-700">Wristband</p>
                            <h2 class="mt-1.5 line-clamp-2 text-lg font-black leading-tight">{{ $ticket->concert->name }}</h2>
                            <p class="mt-1.5 line-clamp-1 text-[10px] text-neutral-600">{{ $ticket->concert->venue }} - {{ $ticket->concert->date->format('d M Y') }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[10px]">
                            <div class="rounded border border-neutral-200 p-1.5">
                                <p class="text-neutral-500">Area</p>
                                <p class="line-clamp-1 font-black">{{ $ticket->order->ticketZone?->name ?? '-' }}</p>
                            </div>
                            <div class="rounded border border-neutral-200 p-1.5">
                                <p class="text-neutral-500">Status</p>
                                <p class="line-clamp-1 font-black">{{ $ticket->wristband->wristband_status }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center gap-2 border-r border-dashed border-neutral-300 bg-stone-50 p-2.5">
                        <div class="rounded bg-white p-2 shadow-sm">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(96)->generate($ticket->wristband->wristband_code) !!}
                        </div>
                        <p class="font-mono text-[9px] font-black">{{ $ticket->wristband->wristband_code }}</p>
                    </div>

                    <div class="flex flex-col justify-between p-4">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-[0.24em] text-orange-700">Holder</p>
                            <h3 class="mt-2 line-clamp-1 text-xl font-black">{{ $ticket->user->name }}</h3>
                            <p class="mt-2 font-mono text-[10px] text-neutral-600">{{ $ticket->ticket_code }}</p>
                        </div>
                        <div class="rounded-md bg-neutral-950 p-3 text-white">
                            <p class="text-[9px] uppercase tracking-[0.18em] text-orange-200">Gate validation</p>
                            <p class="mt-1 text-[11px] font-bold leading-snug">Scan QR gelang satu kali di gate masuk.</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-center bg-orange-700 text-white">
                        <div class="-rotate-90 whitespace-nowrap text-[9px] font-black uppercase tracking-[0.32em]">Valid Entry</div>
                    </div>
                </div>
            </div>
            </div>

            <div class="no-print mt-6 flex flex-wrap gap-3">
                <a href="{{ route('loket.wristbands.download', $ticket->wristband) }}" class="rounded-full bg-neutral-950 px-6 py-3 font-bold text-white">Download PDF</a>
                <a href="{{ route('loket.wristbands.download', ['wristband' => $ticket->wristband, 'mode' => 'print']) }}" class="rounded-full border px-6 py-3 font-bold">Cetak Gelang</a>
                <a class="rounded-full border px-6 py-3 font-bold" href="{{ route('loket.scan') }}">Scan Lagi</a>
            </div>
        @else
            <a class="no-print mt-6 inline-flex rounded-full border px-5 py-3 font-bold" href="{{ route('loket.scan') }}">Scan Lagi</a>
        @endif
    </div>
</section>
@endsection
