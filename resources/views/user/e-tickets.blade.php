@extends('layouts.app', ['title' => 'E-Ticket'])
@section('content')
<section class="mx-auto max-w-7xl px-4 py-12">
    <div>
        <p class="text-sm font-bold uppercase text-orange-700">Tiket digital</p>
        <h1 class="mt-2 text-4xl font-black">E-Ticket Saya</h1>
    </div>

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        @forelse($tickets as $ticket)
            <a class="rounded-lg border bg-white p-5 shadow-sm" href="{{ route('user.tickets.show',$ticket) }}">
                <p class="font-mono text-sm text-neutral-500">{{ $ticket->ticket_code }}</p>
                <h3 class="mt-2 text-xl font-black">{{ $ticket->concert->name }}</h3>
                <div class="mt-4 grid gap-3 rounded-lg bg-neutral-50 p-3 text-sm md:grid-cols-2">
                    <div>
                        <p class="text-neutral-500">Status E-Ticket</p>
                        <p class="font-bold">{{ $ticket->ticket_status }}</p>
                    </div>
                    <div>
                        <p class="text-neutral-500">Status Gelang</p>
                        <p class="font-bold">{{ $ticket->wristband?->wristband_status ?? 'Belum ditukar' }}</p>
                    </div>
                </div>
                @unless($ticket->wristband)
                    <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm font-bold text-amber-900">Tukarkan tiket Anda terlebih dahulu di loket venue.</p>
                @endunless
            </a>
        @empty
            <div class="rounded-lg border bg-white p-8 text-center md:col-span-2">
                <h2 class="text-2xl font-black">Belum ada E-Ticket</h2>
                <p class="mt-2 text-neutral-600">E-Ticket akan muncul setelah pembayaran diverifikasi admin.</p>
            </div>
        @endforelse
    </div>
    <div class="mt-6">{{ $tickets->links() }}</div>
</section>
@endsection
