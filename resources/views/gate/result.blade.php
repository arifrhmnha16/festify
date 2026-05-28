@extends('layouts.app', ['title' => 'Hasil Gate'])
@section('content')
<section class="mx-auto max-w-2xl px-4 py-12"><div class="rounded-lg border bg-white p-8"><p class="text-sm font-bold uppercase {{ $success ? 'text-green-700' : 'text-red-700' }}">{{ $success ? 'Akses diterima' : 'Akses ditolak' }}</p><h1 class="mt-2 text-4xl font-black">{{ $message }}</h1>@if($wristband)<p class="mt-4">{{ $wristband->concert->name }} - {{ $wristband->eTicket->user->name }}</p><p class="font-mono">{{ $wristband->wristband_code }}</p>@endif<a class="mt-6 inline-flex rounded-full border px-5 py-3 font-bold" href="{{ route('gate.scan') }}">Scan Lagi</a></div></section>
@endsection
