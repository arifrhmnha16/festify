@extends('layouts.app', ['title' => $role === 'loket' ? 'Login Loket' : 'Login Gate'])
@section('content')
<section class="mx-auto grid min-h-[70vh] max-w-5xl items-center px-4 py-12 md:grid-cols-2">
    <div>
        <p class="mb-3 text-sm font-bold uppercase tracking-widest text-orange-700">Area petugas</p>
        <h1 class="text-5xl font-black">Login {{ $role === 'loket' ? 'Petugas Loket' : 'Petugas Gate' }}</h1>
        <p class="mt-4 text-neutral-600">{{ $role === 'loket' ? 'Validasi E-Ticket dan aktifkan gelang konser.' : 'Validasi gelang dan izinkan akses masuk venue.' }}</p>
    </div>
    <form method="post" action="{{ route($role.'.login.store') }}" class="rounded-lg border border-neutral-200 bg-white p-6 shadow-sm">
        @csrf
        <label class="block text-sm font-bold">Username</label>
        <input name="username" value="{{ old('username') }}" class="mt-2 w-full rounded-md border border-neutral-300 px-4 py-3" required autofocus>
        <label class="mt-4 block text-sm font-bold">Password</label>
        <input type="password" name="password" class="mt-2 w-full rounded-md border border-neutral-300 px-4 py-3" required>
        <button class="mt-6 w-full rounded-full bg-neutral-950 px-5 py-3 font-bold text-white">Masuk {{ $role === 'loket' ? 'Loket' : 'Gate' }}</button>
    </form>
</section>
@endsection
