@extends('layouts.app', ['title' => 'Konser'])
@section('content')
<section class="mx-auto max-w-6xl px-4 py-10">
    <h1 class="text-4xl font-black">Daftar Konser</h1>
    <form class="mt-6 grid gap-3 rounded-lg border border-neutral-200 bg-white p-4 md:grid-cols-[1fr_1fr_180px_auto]">
        <input name="q" value="{{ request('q') }}" class="rounded-md border border-neutral-300 px-4 py-3" placeholder="Cari konser/artis">
        <input name="venue" value="{{ request('venue') }}" class="rounded-md border border-neutral-300 px-4 py-3" placeholder="Lokasi">
        <input type="date" name="date" value="{{ request('date') }}" class="rounded-md border border-neutral-300 px-4 py-3">
        <button class="rounded-md bg-neutral-950 px-5 py-3 font-bold text-white">Cari</button>
    </form>
    <div class="mt-7 grid gap-6 md:grid-cols-3">@foreach($concerts as $concert)<x-concert-card :concert="$concert" />@endforeach</div>
    <div class="mt-7">{{ $concerts->links() }}</div>
</section>
@endsection
