@extends('layouts.app', ['title' => 'Konser'])
@section('content')
<section class="mx-auto max-w-6xl px-4 py-10">
    <h1 class="text-4xl font-black">Daftar Konser</h1>
    <div class="mt-7 grid gap-8 md:grid-cols-3">@foreach($concerts as $concert)<x-concert-card :concert="$concert" />@endforeach</div>
    <div class="mt-7">{{ $concerts->links() }}</div>
</section>
@endsection
