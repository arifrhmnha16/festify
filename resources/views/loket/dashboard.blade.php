@extends('layouts.app', ['title' => 'Dashboard Loket'])
@section('content')
<section class="mx-auto max-w-5xl px-4 py-12"><h1 class="text-4xl font-black">Dashboard Loket</h1><div class="mt-8 grid gap-4 md:grid-cols-2"><div class="rounded-lg border bg-white p-6"><p>E-Ticket sudah ditukar</p><strong class="text-4xl">{{ $exchanged }}</strong></div><div class="rounded-lg border bg-white p-6"><p>Gelang aktif</p><strong class="text-4xl">{{ $active }}</strong></div></div><a class="mt-8 inline-flex rounded-full bg-neutral-950 px-6 py-3 font-bold text-white" href="{{ route('loket.scan') }}">Scan QR E-Ticket</a></section>
@endsection
