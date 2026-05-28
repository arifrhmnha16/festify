@extends('layouts.admin', ['title' => 'Kelola User', 'pageTitle' => 'Kelola User'])
@section('content')
<section>
    <form method="post" action="{{ route('admin.users.store') }}" class="grid gap-3 rounded-lg border bg-white p-5 md:grid-cols-5">@csrf
        <input name="name" placeholder="Nama" class="rounded border px-3 py-2">
        <input type="email" name="email" placeholder="Email" class="rounded border px-3 py-2">
        <input name="phone" placeholder="Telepon" class="rounded border px-3 py-2">
        <input name="password" placeholder="Password" class="rounded border px-3 py-2">
        <button class="rounded bg-neutral-950 px-4 py-2 font-bold text-white">Tambah User</button>
    </form>
    <div class="mt-6 grid gap-3">@foreach($users as $user)<div class="rounded-lg border bg-white p-4"><form method="post" action="{{ route('admin.users.update',$user) }}" class="grid gap-2 md:grid-cols-5">@csrf @method('put')<input name="name" value="{{ $user->name }}" class="rounded border px-3 py-2"><input type="email" name="email" value="{{ $user->email }}" class="rounded border px-3 py-2"><input name="phone" value="{{ $user->phone }}" class="rounded border px-3 py-2"><input name="password" placeholder="Password baru" class="rounded border px-3 py-2"><button class="rounded bg-orange-700 px-4 py-2 font-bold text-white">Simpan</button></form><form method="post" action="{{ route('admin.users.destroy',$user) }}" class="mt-2">@csrf @method('delete')<button class="text-sm font-bold text-red-700">Hapus User</button></form></div>@endforeach</div><div class="mt-6">{{ $users->links() }}</div>
</section>
@endsection
