@extends('layouts.app', ['title' => 'Pembayaran'])
@section('content')
<section class="mx-auto max-w-3xl px-4 py-12">
    <h1 class="text-4xl font-black">Pembayaran</h1>
    <div class="mt-6 rounded-lg border bg-white p-6">
        <p>Kode pemesanan: <strong>{{ $order->order_code }}</strong></p><p>Konser: <strong>{{ $order->concert->name }}</strong></p><p>Area venue: <strong>{{ $order->ticketZone?->name ?? '-' }}</strong></p><p>Total bayar: <strong>Rp{{ number_format($order->total_price,0,',','.') }}</strong></p><p>Status: <strong>{{ $order->payment->payment_status }}</strong></p>
        <form method="post" action="{{ route('user.payments.submit', $order) }}" class="mt-6">@csrf
            <select name="payment_method" class="w-full rounded-md border px-4 py-3"><option value="transfer_manual">Transfer Manual</option><option value="ewallet">E-Wallet</option></select>
            <button class="mt-5 rounded-full bg-neutral-950 px-6 py-3 font-bold text-white">Bayar & Terbitkan E-Ticket</button>
        </form>
    </div>
</section>
@endsection
