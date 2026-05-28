<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Festify' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-neutral-950 antialiased">
    @php
        $menus = [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '□'],
            ['route' => 'admin.concerts', 'label' => 'Konser', 'icon' => '♪'],
            ['route' => 'admin.users', 'label' => 'User', 'icon' => 'U'],
            ['route' => 'admin.officers', 'label' => 'Petugas', 'icon' => 'P'],
            ['route' => 'admin.orders', 'label' => 'Pemesanan', 'icon' => '#'],
            ['route' => 'admin.payments', 'label' => 'Pembayaran', 'icon' => '$'],
            ['route' => 'admin.tickets', 'label' => 'E-Ticket', 'icon' => 'Q'],
            ['route' => 'admin.wristbands', 'label' => 'Gelang', 'icon' => 'G'],
            ['route' => 'admin.reports', 'label' => 'Laporan', 'icon' => 'R'],
        ];
    @endphp

    <div class="min-h-screen md:grid md:grid-cols-[280px_1fr]">
        <aside class="border-b border-neutral-200 bg-neutral-950 text-white md:sticky md:top-0 md:h-screen md:border-b-0 md:border-r">
            <div class="flex items-center justify-between px-5 py-5 md:block">
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="block h-12 w-44 overflow-hidden rounded-md" aria-label="Festify Admin">
                        <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-full w-full object-cover object-center">
                    </a>
                    <p class="mt-1 text-xs font-bold uppercase tracking-widest text-orange-200">Admin Panel</p>
                </div>
                <form method="post" action="{{ route('logout') }}" class="md:hidden">@csrf<button class="rounded-full border border-white/20 px-4 py-2 text-sm font-bold">Logout</button></form>
            </div>

            <nav class="flex gap-2 overflow-x-auto px-4 pb-4 md:block md:space-y-1 md:overflow-visible md:px-3">
                @foreach($menus as $menu)
                    <a href="{{ route($menu['route']) }}" class="flex shrink-0 items-center gap-3 rounded-lg px-4 py-3 text-sm font-bold transition {{ request()->routeIs($menu['route']) ? 'bg-white text-neutral-950' : 'text-neutral-300 hover:bg-white/10 hover:text-white' }}">
                        <span class="grid h-7 w-7 place-items-center rounded-md {{ request()->routeIs($menu['route']) ? 'bg-orange-100 text-orange-700' : 'bg-white/10 text-orange-200' }}">{{ $menu['icon'] }}</span>
                        {{ $menu['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="mt-auto hidden border-t border-white/10 p-5 md:block">
                <p class="text-sm font-bold">{{ auth('admin')->user()?->name }}</p>
                <p class="text-xs text-neutral-400">{{ auth('admin')->user()?->username }}</p>
                <form method="post" action="{{ route('logout') }}" class="mt-4">@csrf<button class="w-full rounded-full border border-white/20 px-4 py-2 text-sm font-bold hover:bg-white hover:text-neutral-950">Logout</button></form>
            </div>
        </aside>

        <main class="min-w-0">
            <header class="border-b border-neutral-200 bg-white px-4 py-5 md:px-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <div class="mb-2 h-9 w-32 overflow-hidden rounded-md">
                            <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-full w-full object-cover object-center">
                        </div>
                        <h1 class="text-2xl font-black">{{ $pageTitle ?? $title ?? 'Dashboard' }}</h1>
                    </div>
                    <a href="{{ route('home') }}" class="rounded-full border border-neutral-300 px-4 py-2 text-sm font-bold">Lihat Website</a>
                </div>
            </header>

            @if(session('success'))
                <div class="px-4 pt-5 md:px-8"><div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div></div>
            @endif
            @if(session('error') || $errors->any())
                <div class="px-4 pt-5 md:px-8"><div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') ?? $errors->first() }}</div></div>
            @endif

            <div class="px-4 py-8 md:px-8">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
