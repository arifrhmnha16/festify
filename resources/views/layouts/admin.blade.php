<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Festify' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-100 text-neutral-950 antialiased">
    @php
        $menus = [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'D'],
            ['route' => 'admin.concerts', 'label' => 'Konser', 'icon' => 'K'],
            ['route' => 'admin.users', 'label' => 'User', 'icon' => 'U'],
            ['route' => 'admin.officers', 'label' => 'Petugas', 'icon' => 'P'],
            ['route' => 'admin.orders', 'label' => 'Pemesanan', 'icon' => 'O'],
            ['route' => 'admin.payments', 'label' => 'Pembayaran', 'icon' => '$'],
            ['route' => 'admin.tickets', 'label' => 'E-Ticket', 'icon' => 'T'],
            ['route' => 'admin.wristbands', 'label' => 'Gelang', 'icon' => 'G'],
            ['route' => 'admin.reports', 'label' => 'Laporan', 'icon' => 'R'],
        ];
    @endphp

    <div class="min-h-screen lg:grid lg:grid-cols-[18rem_1fr]">
        <aside class="border-b border-neutral-200 bg-white lg:sticky lg:top-0 lg:h-screen lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between px-5 py-5">
                <a href="{{ route('admin.dashboard') }}" class="block h-12 w-40 overflow-hidden rounded-lg border border-neutral-200 bg-white" aria-label="Festify Admin">
                    <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-full w-full object-cover object-center">
                </a>
                <form method="post" action="{{ route('logout') }}" class="lg:hidden">@csrf<button class="fi-btn-muted">Logout</button></form>
            </div>

            <div class="px-5 pb-4">
                <div class="rounded-xl border border-orange-200 bg-orange-50 px-4 py-3">
                    <p class="text-sm font-black text-orange-900">{{ auth('admin')->user()?->name ?? 'Admin Festify' }}</p>
                    <p class="text-xs text-orange-700">{{ auth('admin')->user()?->username ?? 'admin' }}</p>
                </div>
            </div>

            <nav class="flex gap-2 overflow-x-auto px-4 pb-4 lg:block lg:space-y-1 lg:overflow-visible">
                @foreach($menus as $menu)
                    @php($active = request()->routeIs($menu['route']))
                    <a href="{{ route($menu['route']) }}" class="flex shrink-0 items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition {{ $active ? 'bg-neutral-950 text-white shadow-sm' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-950' }}">
                        <span class="grid h-8 w-8 place-items-center rounded-lg text-xs {{ $active ? 'bg-white/15 text-white' : 'bg-neutral-100 text-neutral-500' }}">{{ $menu['icon'] }}</span>
                        <span>{{ $menu['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="hidden px-4 pb-5 lg:block">
                <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-3">
                    <a href="{{ route('home') }}" class="fi-btn-muted w-full">Lihat Website</a>
                    <form method="post" action="{{ route('logout') }}" class="mt-2">@csrf<button class="fi-btn-dark w-full">Logout</button></form>
                </div>
            </div>
        </aside>

        <main class="min-w-0">
            <header class="sticky top-0 z-30 border-b border-neutral-200 bg-white/90 px-4 py-4 backdrop-blur md:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-neutral-500">Festify Admin</p>
                        <h1 class="mt-1 text-2xl font-black">{{ $pageTitle ?? $title ?? 'Dashboard' }}</h1>
                    </div>
                    <a href="{{ route('home') }}" class="fi-btn-muted">Lihat Website</a>
                </div>
            </header>

            @if(session('success'))
                <div class="px-4 pt-5 md:px-8"><div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-bold text-green-800">{{ session('success') }}</div></div>
            @endif
            @if(session('error') || $errors->any())
                <div class="px-4 pt-5 md:px-8"><div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-800">{{ session('error') ?? $errors->first() }}</div></div>
            @endif

            <div class="px-4 py-6 md:px-8">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
