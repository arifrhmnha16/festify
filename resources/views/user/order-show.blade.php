@extends('layouts.app', ['title' => $order->order_code])
@section('content')
<section class="mx-auto max-w-4xl px-4 py-12"><h1 class="text-4xl font-black">{{ $order->order_code }}</h1>
<div class="mt-6 rounded-lg border bg-white p-6"><p>{{ $order->concert->name }}</p><p>Area venue: <strong>{{ $order->ticketZone?->name ?? '-' }}</strong></p><p>Total: <strong>Rp{{ number_format($order->total_price,0,',','.') }}</strong></p><p>Status pemesanan: {{ $order->order_status }}</p><p>Status pembayaran: {{ $order->payment?->payment_status }}</p><a class="mt-4 inline-flex rounded-full border px-5 py-3 font-bold" href="{{ route('user.payments.show',$order) }}">Pembayaran</a></div>
<div class="mt-6"><x-venue-map :zones="$order->concert->ticketZones" :selected-zone-id="$order->ticket_zone_id" /></div>
<h2 class="mt-8 text-2xl font-black">E-Ticket</h2><div class="mt-4 grid gap-3">@foreach($order->eTickets as $ticket)<a class="rounded-lg border bg-white p-4 font-mono" href="{{ route('user.tickets.show',$ticket) }}">{{ $ticket->ticket_code }} - {{ $ticket->ticket_status }}</a>@endforeach</div></section>
@endsection
