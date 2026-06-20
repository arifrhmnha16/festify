@extends('layouts.app', ['title' => 'Lupa Password'])
@section('content')
<section class="mx-auto grid min-h-[70vh] max-w-5xl items-center px-4 py-12 md:grid-cols-2">
    <div>
        <h1 class="text-5xl font-black">Reset Password</h1>
        <p class="mt-4 text-neutral-600">Masukkan email akun user. Sistem akan mengirim link untuk membuat password baru.</p>
    </div>
    <form method="post" action="{{ route('password.email') }}" class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
        @csrf
        <label class="block text-sm font-bold">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="mt-2 w-full rounded-md border border-neutral-300 px-4 py-3" required autofocus>
        <button class="mt-6 w-full rounded-full bg-neutral-950 px-5 py-3 font-bold text-white">Kirim Link Reset</button>
        <p class="mt-4 text-center text-sm text-neutral-600"><a class="font-bold text-orange-700" href="{{ route('login') }}">Kembali ke login</a></p>
    </form>
</section>
@endsection
