@extends('layouts.admin', ['title' => 'Kelola User', 'pageTitle' => 'Kelola User'])
@section('content')
<section class="grid gap-6">
    <form method="post" action="{{ route('admin.users.store') }}" class="rounded-lg border bg-white p-5 shadow-sm">
        @csrf
        <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-lg font-black">Tambah User</h2>
            <button class="fi-btn-dark">Tambah</button>
        </div>
        <div class="grid gap-3 md:grid-cols-4">
            <input name="name" placeholder="Nama" class="rounded-md border border-neutral-300 px-3 py-2" required>
            <input type="email" name="email" placeholder="Email" class="rounded-md border border-neutral-300 px-3 py-2" required>
            <input name="phone" placeholder="Telepon" class="rounded-md border border-neutral-300 px-3 py-2">
            <input name="password" type="password" placeholder="Password" class="rounded-md border border-neutral-300 px-3 py-2" required>
        </div>
    </form>

    <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="fi-table min-w-[920px]">
            <thead class="bg-neutral-100 text-xs uppercase tracking-wide text-neutral-600">
                <tr>
                    <th class="p-3">ID</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Telepon</th>
                    <th class="p-3">Password Baru</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-t align-top">
                        <td class="p-3 font-mono text-neutral-500">#{{ $user->id }}</td>
                        <td class="p-3"><input form="user-update-{{ $user->id }}" name="name" value="{{ $user->name }}" class="w-full rounded-md border border-neutral-300 px-3 py-2" required></td>
                        <td class="p-3"><input form="user-update-{{ $user->id }}" type="email" name="email" value="{{ $user->email }}" class="w-full rounded-md border border-neutral-300 px-3 py-2" required></td>
                        <td class="p-3"><input form="user-update-{{ $user->id }}" name="phone" value="{{ $user->phone }}" class="w-full rounded-md border border-neutral-300 px-3 py-2"></td>
                        <td class="p-3"><input form="user-update-{{ $user->id }}" name="password" type="password" placeholder="Kosongkan jika tidak diganti" class="w-full rounded-md border border-neutral-300 px-3 py-2"></td>
                        <td class="p-3">
                            <form id="user-update-{{ $user->id }}" method="post" action="{{ route('admin.users.update',$user) }}" class="hidden">@csrf @method('put')</form>
                            <div class="flex justify-end gap-2">
                                <button form="user-update-{{ $user->id }}" class="fi-btn-primary">Simpan</button>
                                <form method="post" action="{{ route('admin.users.destroy',$user) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="fi-btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-neutral-500">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $users->links() }}</div>
</section>
@endsection

