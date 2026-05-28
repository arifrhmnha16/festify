@extends('layouts.app', ['title' => 'Riwayat Pemesanan'])
@section('content')
<section class="mx-auto max-w-7xl px-4 py-12"><h1 class="text-4xl font-black">Riwayat Pemesanan</h1>
<div class="mt-6 overflow-hidden rounded-lg border bg-white"><table class="w-full text-left text-sm"><tr class="bg-neutral-100"><th class="p-3">Kode</th><th>Konser</th><th>Area</th><th>Total</th><th>Status</th><th>Pembayaran</th><th></th></tr>@foreach($orders as $order)<tr class="border-t"><td class="p-3 font-mono">{{ $order->order_code }}</td><td>{{ $order->concert->name }}</td><td>{{ $order->ticketZone?->name ?? '-' }}</td><td>Rp{{ number_format($order->total_price,0,',','.') }}</td><td>{{ $order->order_status }}</td><td>{{ $order->payment?->payment_status }}</td><td><a class="font-bold text-orange-700" href="{{ route('user.orders.show',$order) }}">Detail</a></td></tr>@endforeach</table></div><div class="mt-6">{{ $orders->links() }}</div></section>
@endsection
