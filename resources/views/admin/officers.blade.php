@extends('layouts.admin', ['title' => 'Kelola Petugas', 'pageTitle' => 'Kelola Petugas'])
@section('content')
<section class="grid gap-6">
    <form method="post" action="{{ route('admin.officers.store') }}" class="rounded-lg border bg-white p-5 shadow-sm">
        @csrf
        <div class="mb-4 flex items-center justify-between gap-3">
            <h2 class="text-lg font-black">Tambah Petugas</h2>
            <button class="fi-btn-dark">Tambah</button>
        </div>
        <div class="grid gap-3 md:grid-cols-4">
            <input name="name" placeholder="Nama" class="rounded-md border border-neutral-300 px-3 py-2" required>
            <input name="username" placeholder="Username" class="rounded-md border border-neutral-300 px-3 py-2" required>
            <input name="password" type="password" placeholder="Password" class="rounded-md border border-neutral-300 px-3 py-2" required>
            <select name="role" class="rounded-md border border-neutral-300 px-3 py-2" required>
                <option value="loket">Loket</option>
                <option value="gate">Gate</option>
            </select>
        </div>
    </form>

    <div class="overflow-x-auto rounded-lg border bg-white shadow-sm">
        <table class="fi-table min-w-[860px]">
            <thead class="bg-neutral-100 text-xs uppercase tracking-wide text-neutral-600">
                <tr>
                    <th class="p-3">ID</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3">Username</th>
                    <th class="p-3">Role</th>
                    <th class="p-3">Password Baru</th>
                    <th class="p-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($officers as $officer)
                    <tr class="border-t align-top">
                        <td class="p-3 font-mono text-neutral-500">#{{ $officer->id }}</td>
                        <td class="p-3"><input form="officer-update-{{ $officer->id }}" name="name" value="{{ $officer->name }}" class="w-full rounded-md border border-neutral-300 px-3 py-2" required></td>
                        <td class="p-3"><input form="officer-update-{{ $officer->id }}" name="username" value="{{ $officer->username }}" class="w-full rounded-md border border-neutral-300 px-3 py-2" required></td>
                        <td class="p-3">
                            <select form="officer-update-{{ $officer->id }}" name="role" class="w-full rounded-md border border-neutral-300 px-3 py-2">
                                <option value="loket" @selected($officer->role==='loket')>Loket</option>
                                <option value="gate" @selected($officer->role==='gate')>Gate</option>
                            </select>
                        </td>
                        <td class="p-3"><input form="officer-update-{{ $officer->id }}" name="password" type="password" placeholder="Kosongkan jika tidak diganti" class="w-full rounded-md border border-neutral-300 px-3 py-2"></td>
                        <td class="p-3">
                            <form id="officer-update-{{ $officer->id }}" method="post" action="{{ route('admin.officers.update',$officer) }}" class="hidden">@csrf @method('put')</form>
                            <div class="flex justify-end gap-2">
                                <button form="officer-update-{{ $officer->id }}" class="fi-btn-primary">Simpan</button>
                                <form method="post" action="{{ route('admin.officers.destroy',$officer) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="fi-btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-neutral-500">Belum ada petugas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div>{{ $officers->links() }}</div>
</section>
@endsection

