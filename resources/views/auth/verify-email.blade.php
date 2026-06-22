@extends('layouts.app', ['title' => 'Verifikasi Email'])
@section('content')
<section class="mx-auto max-w-3xl px-4 py-16">
    <div class="rounded-lg border border-neutral-200 bg-white p-8 shadow-sm">
        <p class="text-sm font-bold uppercase text-orange-700">Verifikasi Email</p>
        <h1 class="mt-2 text-4xl font-black">Cek inbox kamu</h1>
        <p class="mt-4 text-neutral-600">Sebelum masuk dashboard user, verifikasi email akun melalui link yang sudah dikirim. Kalau belum menerima email, kirim ulang dari tombol di bawah.</p>
        @if($localVerificationUrl)
            <div class="mt-6 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                <p class="font-bold">Mode verifikasi langsung aktif</p>
                <p class="mt-1">Email belum dipakai untuk verifikasi. Klik tombol di bawah untuk melanjutkan.</p>
                <a href="{{ $localVerificationUrl }}" class="mt-4 inline-flex rounded-full bg-amber-700 px-5 py-3 font-bold text-white">Verifikasi Sekarang</a>
            </div>
        @endif
        <form method="post" action="{{ route('verification.send') }}" class="mt-6">
            @csrf
            <button class="rounded-full bg-neutral-950 px-5 py-3 font-bold text-white">Kirim Ulang Link</button>
        </form>
    </div>
</section>
@endsection
