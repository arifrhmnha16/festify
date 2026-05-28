@extends('layouts.app', ['title' => 'Register'])
@section('content')
<section class="mx-auto grid min-h-[70vh] max-w-5xl items-center px-4 py-12 md:grid-cols-2">
    <div><h1 class="text-5xl font-black">Buat Akun User</h1><p class="mt-4 text-neutral-600">Pesan tiket, unggah pembayaran, dan simpan E-Ticket dalam satu dashboard.</p></div>
    <form method="post" action="{{ route('register.store') }}" class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
        @csrf
        <input name="name" value="{{ old('name') }}" class="w-full rounded-md border border-neutral-300 px-4 py-3" placeholder="Nama lengkap" required>
        <input type="email" name="email" value="{{ old('email') }}" class="mt-3 w-full rounded-md border border-neutral-300 px-4 py-3" placeholder="Email" required>
        <input name="phone" value="{{ old('phone') }}" class="mt-3 w-full rounded-md border border-neutral-300 px-4 py-3" placeholder="Nomor telepon">
        <input type="password" name="password" class="mt-3 w-full rounded-md border border-neutral-300 px-4 py-3" placeholder="Password" required>
        <input type="password" name="password_confirmation" class="mt-3 w-full rounded-md border border-neutral-300 px-4 py-3" placeholder="Konfirmasi password" required>
        <button class="mt-6 w-full rounded-full bg-neutral-950 px-5 py-3 font-bold text-white">Register</button>
    </form>
</section>
@endsection
