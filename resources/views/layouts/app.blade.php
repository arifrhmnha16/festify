<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Festify' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 pb-24 text-neutral-950 antialiased md:pb-0">
    <header class="sticky top-0 z-40 border-b border-neutral-200 bg-white/95 px-4 py-3 backdrop-blur md:hidden print:hidden">
        <div class="mx-auto flex max-w-md items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="block h-10 w-36 overflow-hidden rounded-md" aria-label="Festify">
                <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-full w-full object-cover object-center">
            </a>
            @auth
                <p class="truncate text-right text-xs font-bold text-neutral-600">Halo, {{ auth()->user()->name }}</p>
            @else
                <a href="{{ route('login') }}" class="rounded-full border border-neutral-300 px-4 py-2 text-xs font-bold">Login</a>
            @endauth
        </div>
    </header>

    <nav class="sticky top-0 z-40 hidden border-b border-neutral-200 bg-white/95 backdrop-blur md:block">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
            <a href="{{ route('home') }}" class="block h-10 w-36 overflow-hidden rounded-md" aria-label="Festify">
                <img src="{{ asset('logofest.png') }}" alt="Festify" class="h-full w-full object-cover object-center">
            </a>
            <div class="hidden items-center gap-6 text-sm font-semibold md:flex">
                <a href="{{ route('home') }}" class="hover:text-orange-700">Beranda</a>
                <a href="{{ route('concerts.index') }}" class="hover:text-orange-700">Konser</a>
                <a href="{{ route('home') }}#cara-kerja" class="hover:text-orange-700">Cara Kerja</a>
                @auth
                    <a href="{{ route('user.tickets') }}" class="rounded-full bg-orange-700 px-4 py-2 font-bold text-white hover:bg-orange-800">Tiket Saya</a>
                    <a href="{{ route('user.wristbands') }}" class="hover:text-orange-700">Gelang Saya</a>
                @endauth
                @if(auth('admin')->check())
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-orange-700">Admin</a>
                @endif
                @if(auth('officer')->check())
                    <a href="{{ auth('officer')->user()->role === 'loket' ? route('loket.dashboard') : route('gate.dashboard') }}" class="hover:text-orange-700">Petugas</a>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if(auth()->check() || auth('admin')->check() || auth('officer')->check())
                    <form method="post" action="{{ route('logout') }}">@csrf<button class="rounded-full border border-neutral-300 px-4 py-2 text-sm font-semibold">Logout</button></form>
                @else
                    <a href="{{ route('login') }}" class="rounded-full border border-neutral-300 px-4 py-2 text-sm font-semibold">Login</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-neutral-950 px-4 py-2 text-sm font-semibold text-white">Cari Tiket</a>
                @endif
            </div>
        </div>
    </nav>

    <main>
        @if(session('success'))
            <div class="mx-auto mt-4 max-w-6xl px-4"><div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div></div>
        @endif
        @if(session('error') || $errors->any())
            <div class="mx-auto mt-4 max-w-6xl px-4"><div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') ?? $errors->first() }}</div></div>
        @endif
        @yield('content')
    </main>

    <footer class="mt-12 border-t border-neutral-200 bg-white">
        <div class="mx-auto grid max-w-6xl gap-5 px-4 py-7 md:grid-cols-3">
            <div><img src="{{ asset('logofest.png') }}" alt="Festify" class="h-10 w-36 rounded-md object-cover object-center"><p class="mt-3 text-sm text-neutral-600">Sistem e-ticket konser, penukaran gelang, dan validasi gate.</p></div>
            <div class="text-sm text-neutral-600"><strong class="text-neutral-950">Kontak</strong><br>halo@festify.test<br>Jakarta, Indonesia</div>
            <div class="text-sm text-neutral-600"><strong class="text-neutral-950">Sosial</strong><br>Instagram / X / TikTok: @festify</div>
        </div>
    </footer>

    <nav class="fixed inset-x-0 bottom-0 z-50 border-t border-neutral-200 bg-white/95 px-3 py-2 shadow-[0_-8px_24px_rgba(0,0,0,0.08)] backdrop-blur md:hidden print:hidden">
        <div class="mx-auto grid max-w-md grid-cols-4 gap-1 text-[11px] font-bold text-neutral-600">
            <a href="{{ route('home') }}" class="grid justify-items-center gap-1 rounded-lg px-2 py-2 {{ request()->routeIs('home') ? 'text-orange-700' : '' }}" aria-label="Beranda">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/></svg>
                <span>Beranda</span>
            </a>
            <a href="{{ route('concerts.index') }}" class="grid justify-items-center gap-1 rounded-lg px-2 py-2 {{ request()->routeIs('concerts.*') || request()->routeIs('user.concerts*') ? 'text-orange-700' : '' }}" aria-label="Konser">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                <span>Konser</span>
            </a>
            @if(auth()->check())
                <a href="{{ route('user.tickets') }}" class="grid justify-items-center gap-1 rounded-lg px-2 py-2 {{ request()->routeIs('user.tickets*') ? 'text-orange-700' : '' }}" aria-label="Tiket Saya">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4Z"/><path d="M9 8h6"/><path d="M9 16h6"/></svg>
                    <span>Tiket</span>
                </a>
                <a href="{{ route('user.wristbands') }}" class="grid justify-items-center gap-1 rounded-lg px-2 py-2 {{ request()->routeIs('user.wristbands') ? 'text-orange-700' : '' }}" aria-label="Gelang Saya">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 7a5 5 0 0 1 10 0"/><path d="M7 7v10a5 5 0 0 0 10 0V7"/><path d="M9 12h6"/></svg>
                    <span>Gelang</span>
                </a>
            @elseif(auth('admin')->check())
                <a href="{{ route('admin.dashboard') }}" class="grid justify-items-center gap-1 rounded-lg px-2 py-2" aria-label="Admin">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M4 10h16"/><path d="M10 20V10"/></svg>
                    <span>Admin</span>
                </a>
                <form method="post" action="{{ route('logout') }}" class="grid justify-items-center gap-1 rounded-lg px-2 py-2">@csrf<button aria-label="Logout" class="grid justify-items-center gap-1"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M21 3v18"/></svg><span>Logout</span></button></form>
            @elseif(auth('officer')->check())
                <a href="{{ auth('officer')->user()->role === 'loket' ? route('loket.dashboard') : route('gate.dashboard') }}" class="grid justify-items-center gap-1 rounded-lg px-2 py-2" aria-label="Petugas">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16"/><path d="M6 7v13h12V7"/><path d="M9 7V4h6v3"/></svg>
                    <span>Petugas</span>
                </a>
                <form method="post" action="{{ route('logout') }}" class="grid justify-items-center gap-1 rounded-lg px-2 py-2">@csrf<button aria-label="Logout" class="grid justify-items-center gap-1"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M21 3v18"/></svg><span>Logout</span></button></form>
            @else
                <a href="{{ route('login') }}" class="grid justify-items-center gap-1 rounded-lg px-2 py-2" aria-label="Login">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
                    <span>Login</span>
                </a>
                <a href="{{ route('register') }}" class="grid justify-items-center gap-1 rounded-lg px-2 py-2" aria-label="Daftar">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M22 11h-6"/></svg>
                    <span>Daftar</span>
                </a>
            @endif
        </div>
    </nav>
</body>
</html>
