@extends('layouts.app', ['title' => 'Verifikasi Email'])
@section('content')
<section class="mx-auto max-w-3xl px-4 py-16">
    <div class="rounded-lg border border-neutral-200 bg-white p-8 shadow-sm">
        <p class="text-sm font-bold uppercase text-orange-700">Verifikasi Email</p>
        <h1 class="mt-2 text-4xl font-black">Cek inbox kamu</h1>
        <p class="mt-4 text-neutral-600">Sebelum masuk dashboard user, verifikasi email akun melalui link yang sudah dikirim. Kalau belum menerima email, kirim ulang dari tombol di bawah.</p>
        <form method="post" action="{{ route('verification.send') }}" class="mt-6">
            @csrf
            <button class="rounded-full bg-neutral-950 px-5 py-3 font-bold text-white">Kirim Ulang Link</button>
        </form>
    </div>
</section>
@endsection
