@extends('layouts.app', ['title' => 'Verifikasi Email'])
@section('content')
<section class="mx-auto max-w-3xl px-4 pb-32 pt-8 md:py-16">
    <div class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm md:p-8">
        <p class="text-xs font-bold uppercase tracking-wide text-orange-700 md:text-sm">Verifikasi Email</p>
        <h1 class="mt-2 text-3xl font-black leading-tight md:text-4xl">Cek inbox kamu</h1>
        <p class="mt-3 text-sm leading-7 text-neutral-600 md:mt-4 md:text-base">Sebelum masuk dashboard user, verifikasi email akun melalui link yang sudah dikirim. Kalau belum menerima email, kirim ulang dari tombol di bawah.</p>
        @if($localVerificationUrl)
            <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 md:mt-6">
                <p class="font-bold">Mode verifikasi langsung aktif</p>
                <p class="mt-1">Email belum dipakai untuk verifikasi. Klik tombol di bawah untuk melanjutkan.</p>
                <a href="{{ $localVerificationUrl }}" class="mt-4 inline-flex w-full justify-center rounded-full bg-amber-700 px-5 py-3 font-bold text-white md:w-auto">Verifikasi Sekarang</a>
            </div>
        @endif
        <form method="post" action="{{ route('verification.send') }}" class="mt-5 md:mt-6">
            @csrf
            <button class="w-full rounded-full bg-neutral-950 px-5 py-3 font-bold text-white md:w-auto">Kirim Ulang Link</button>
        </form>
    </div>
</section>
@endsection
