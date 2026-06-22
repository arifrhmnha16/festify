@extends('layouts.app', ['title' => $order->order_code])
@section('content')
@php
    $paymentStatus = $order->payment?->payment_status ?? 'pending';
    $statusStyles = [
        'pending' => 'border-amber-200 bg-amber-50 text-amber-800',
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'failed' => 'border-red-200 bg-red-50 text-red-800',
    ];
@endphp
<section class="mx-auto max-w-5xl px-4 py-12">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-orange-700">Detail pemesanan</p>
            <h1 class="mt-2 text-4xl font-black">{{ $order->order_code }}</h1>
        </div>
        <span class="rounded-full border px-4 py-2 text-sm font-black {{ $statusStyles[$paymentStatus] ?? 'border-neutral-200 bg-neutral-50 text-neutral-700' }}">{{ $paymentStatus }}</span>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_320px]">
        <div class="rounded-lg border bg-white p-6 shadow-sm">
            <h2 class="text-2xl font-black">{{ $order->concert->name }}</h2>
            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-sm text-neutral-500">Area venue</p>
                    <p class="font-bold">{{ $order->ticketZone?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-500">Jumlah tiket</p>
                    <p class="font-bold">{{ $order->ticket_quantity }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-500">Status pemesanan</p>
                    <p class="font-bold">{{ $order->order_status }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-500">Total</p>
                    <p class="font-black">Rp{{ number_format($order->total_price,0,',','.') }}</p>
                </div>
            </div>
            <a class="mt-6 inline-flex rounded-full bg-neutral-950 px-5 py-3 font-bold text-white" href="{{ route('user.payments.show',$order) }}">Buka Pembayaran</a>
        </div>

        <aside class="rounded-lg border bg-white p-6 shadow-sm">
            <h2 class="text-xl font-black">Status E-Ticket</h2>
            @if($paymentStatus === 'success')
                <p class="mt-2 text-sm text-neutral-600">Pembayaran sudah diverifikasi. E-Ticket sudah tersedia.</p>
            @elseif($paymentStatus === 'failed')
                <p class="mt-2 text-sm text-red-700">Pembayaran gagal atau kedaluwarsa. Buka pembayaran untuk membuat transaksi Midtrans baru.</p>
            @else
                <p class="mt-2 text-sm text-neutral-600">E-Ticket akan terbit otomatis setelah transaksi Midtrans berhasil.</p>
            @endif
        </aside>
    </div>

    <div class="mt-6"><x-venue-map :zones="$order->concert->ticketZones" :selected-zone-id="$order->ticket_zone_id" /></div>

    <h2 class="mt-8 text-2xl font-black">E-Ticket</h2>
    <div class="mt-4 grid gap-3">
        @forelse($order->eTickets as $ticket)
            <a class="rounded-lg border bg-white p-4 font-mono shadow-sm" href="{{ route('user.tickets.show',$ticket) }}">{{ $ticket->ticket_code }} - {{ $ticket->ticket_status }}</a>
        @empty
            <div class="rounded-lg border border-dashed bg-white p-5 text-sm text-neutral-600">Belum ada E-Ticket untuk pesanan ini.</div>
        @endforelse
    </div>
</section>
@endsection
