@extends('layouts.admin', ['title' => 'Admin', 'pageTitle' => 'Dashboard Admin'])
@section('content')
<section><div class="grid gap-4 md:grid-cols-4">@foreach($stats as $label=>$value)<div class="rounded-lg border bg-white p-5"><p class="text-sm text-neutral-600">{{ $label }}</p><strong class="text-3xl">{{ $value }}</strong></div>@endforeach</div>
<div class="mt-8 flex flex-wrap gap-2">@foreach(['concerts'=>'Konser','users'=>'User','officers'=>'Petugas','orders'=>'Pemesanan','payments'=>'Pembayaran','tickets'=>'E-Ticket','wristbands'=>'Gelang','reports'=>'Laporan'] as $route=>$label)<a class="rounded-full border bg-white px-4 py-2 font-bold" href="{{ route('admin.'.$route) }}">{{ $label }}</a>@endforeach</div></section>
@endsection
