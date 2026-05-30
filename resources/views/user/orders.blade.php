@extends('layouts.app', ['title' => 'Riwayat Pemesanan'])
@section('content')
@php
    $statusStyles = [
        'pending' => 'border-amber-200 bg-amber-50 text-amber-800',
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'failed' => 'border-red-200 bg-red-50 text-red-800',
    ];
@endphp
<section class="mx-auto max-w-7xl px-4 py-12">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-orange-700">Riwayat transaksi</p>
            <h1 class="mt-2 text-4xl font-black">Riwayat Pemesanan</h1>
        </div>
        <a href="{{ route('user.concerts') }}" class="rounded-full bg-neutral-950 px-5 py-3 text-sm font-bold text-white">Cari Konser</a>
    </div>

    <div class="mt-6 grid gap-4">
        @forelse($orders as $order)
            @php($paymentStatus = $order->payment?->payment_status ?? 'pending')
            <a href="{{ route('user.orders.show',$order) }}" class="block rounded-lg border bg-white p-5 shadow-sm transition hover:border-orange-300">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="font-mono text-sm text-neutral-500">{{ $order->order_code }}</p>
                        <h2 class="mt-1 text-xl font-black">{{ $order->concert->name }}</h2>
                        <p class="mt-1 text-sm text-neutral-600">{{ $order->ticketZone?->name ?? '-' }} - {{ $order->ticket_quantity }} tiket</p>
                    </div>
                    <span class="rounded-full border px-3 py-1 text-sm font-black {{ $statusStyles[$paymentStatus] ?? 'border-neutral-200 bg-neutral-50 text-neutral-700' }}">{{ $paymentStatus }}</span>
                </div>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-neutral-100 pt-4 text-sm">
                    <p class="font-black">Rp{{ number_format($order->total_price,0,',','.') }}</p>
                    <p class="text-neutral-500">Status pesanan: {{ $order->order_status }}</p>
                </div>
            </a>
        @empty
            <div class="rounded-lg border border-dashed bg-white p-8 text-center text-neutral-600">Belum ada pemesanan.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $orders->links() }}</div>
</section>
@endsection
