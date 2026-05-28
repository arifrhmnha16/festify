@extends('layouts.app', ['title' => 'Login'])
@section('content')
<section class="mx-auto grid min-h-[70vh] max-w-5xl items-center px-4 py-12 md:grid-cols-2">
    <div><h1 class="text-5xl font-black">Masuk User</h1><p class="mt-4 text-neutral-600">Login untuk membeli tiket, mengirim bukti pembayaran, dan melihat E-Ticket.</p></div>
    <form method="post" action="{{ route('login.store') }}" class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
        @csrf
        <label class="block text-sm font-bold">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="mt-2 w-full rounded-md border border-neutral-300 px-4 py-3" required>
        <label class="mt-4 block text-sm font-bold">Password</label>
        <input type="password" name="password" class="mt-2 w-full rounded-md border border-neutral-300 px-4 py-3" required>
        <button class="mt-6 w-full rounded-full bg-neutral-950 px-5 py-3 font-bold text-white">Login</button>
        <p class="mt-4 text-center text-sm text-neutral-600">Belum punya akun? <a class="font-bold text-orange-700" href="{{ route('register') }}">Register</a></p>
    </form>
</section>
@endsection
