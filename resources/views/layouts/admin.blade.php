<!doctype html>
<html lang="id" x-data="{ sidebarOpen: false }" x-cloak>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                    },
                },
            },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-screen overflow-hidden bg-slate-50 text-slate-800">
    @php
        $lowStockCount = 0;
        $unreadNotifications = 0;

        if (auth()->user()->role === 'owner') {
            $unreadNotifications = \App\Models\AppNotification::query()
                ->where('user_id', auth()->id())
                ->where('type', 'restock_request')
                ->whereNull('read_at')
                ->count();
        } else {
            $lowStockCount = \App\Models\Product::query()
                ->whereColumn('stock', '<=', 'min_stock')
                ->count();

            $unreadNotifications = \App\Models\AppNotification::query()
                ->where('user_id', auth()->id())
                ->whereNull('read_at')
                ->count();
        }

        $notificationBadgeCount = $unreadNotifications + $lowStockCount;
    @endphp

    <div class="relative h-screen">
        <div class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"></div>

        <aside class="fixed inset-y-0 left-0 z-50 w-72 -translate-x-full bg-gradient-to-b from-primary-900 via-primary-800 to-primary-900 shadow-lg ring-1 ring-primary-900/30 transition-transform lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="flex h-16 items-center justify-between gap-3 border-b border-white/10 px-5">
                <div class="flex items-center gap-3">
                    <div class="grid h-10 w-10 place-items-center rounded-xl bg-white/15 text-white font-bold ring-1 ring-white/10">D</div>
                    <div class="leading-tight">
                        <div class="text-sm font-semibold text-white">Percetakan Dyacom</div>
                        <div class="text-xs text-white/70">Manajemen Persediaan</div>
                    </div>
                </div>
                <button type="button" class="grid h-9 w-9 place-items-center rounded-xl border border-white/15 bg-white/10 text-white hover:bg-white/15 lg:hidden" @click="sidebarOpen = false">
                    <span class="sr-only">Tutup</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <nav class="h-[calc(100vh-4rem)] flex flex-col">
                <div class="flex-1 overflow-y-auto px-3 py-4">
                    <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white ring-1 ring-white/15' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80 group-hover:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM4 10a6 6 0 0111.446-2H10v6H4z" />
                        </svg>
                        <span class="flex-1">Dashboard</span>
                    </a>

                    @if(auth()->user()->role === 'owner')
                        <a href="{{ route('users.index') }}" class="group flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-semibold transition {{ request()->routeIs('users.*') ? 'bg-white/15 text-white ring-1 ring-white/15' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80 group-hover:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 10a4 4 0 100-8 4 4 0 000 8z" />
                                <path fill-rule="evenodd" d="M.458 16.944A10 10 0 0110 12c3.257 0 6.125 1.55 7.942 3.944A8 8 0 111.58 16.944z" clip-rule="evenodd" />
                            </svg>
                            <span class="flex-1">Data Akun</span>
                        </a>
                    @endif
                    @if(auth()->user()->role !== 'owner')
                        <a href="{{ route('products.index') }}" class="group flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-semibold transition {{ request()->routeIs('products.*') ? 'bg-white/15 text-white ring-1 ring-white/15' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80 group-hover:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M4 3a2 2 0 00-2 2v2a1 1 0 001 1h14a1 1 0 001-1V5a2 2 0 00-2-2H4z" />
                                <path fill-rule="evenodd" d="M18 9H2v6a2 2 0 002 2h12a2 2 0 002-2V9z" clip-rule="evenodd" />
                            </svg>
                            <span class="flex-1">Data Produk</span>
                        </a>

                        <a href="{{ route('stock.index') }}" class="group flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-semibold transition {{ request()->routeIs('stock.*') ? 'bg-white/15 text-white ring-1 ring-white/15' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80 group-hover:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 11a1 1 0 011-1h5a1 1 0 010 2H3a1 1 0 01-1-1z" />
                                <path d="M12 7a1 1 0 011-1h5a1 1 0 110 2h-5a1 1 0 01-1-1z" />
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v6a1 1 0 102 0V7z" clip-rule="evenodd" />
                            </svg>
                            <span class="flex-1">Catat Stok</span>
                        </a>
                    @endif

                    <a href="{{ route('restock.index') }}" class="group flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-semibold transition {{ request()->routeIs('restock.*') ? 'bg-white/15 text-white ring-1 ring-white/15' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80 group-hover:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4H6a1 1 0 100 2h3v3a1 1 0 102 0v-3h3a1 1 0 100-2h-3V6z" clip-rule="evenodd" />
                        </svg>
                        <span class="flex-1">{{ auth()->user()->role === 'owner' ? 'Restok (Persetujuan)' : 'Pengajuan Restok' }}</span>
                    </a>

                    @if(auth()->user()->role === 'owner')
                        <a href="{{ route('suppliers.index') }}" class="group flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-semibold transition {{ request()->routeIs('suppliers.*') ? 'bg-white/15 text-white ring-1 ring-white/15' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80 group-hover:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M8 2a1 1 0 00-1 1v1H5a2 2 0 00-2 2v2h14V6a2 2 0 00-2-2h-2V3a1 1 0 00-1-1H8z" />
                                <path d="M3 10v6a2 2 0 002 2h10a2 2 0 002-2v-6H3z" />
                            </svg>
                            <span class="flex-1">Data Supplier</span>
                        </a>
                    @endif

                    @if(auth()->user()->role !== 'owner')
                        <a href="{{ route('notifications.index') }}" class="group flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-semibold transition {{ request()->routeIs('notifications.*') ? 'bg-white/15 text-white ring-1 ring-white/15' : 'text-white/85 hover:bg-white/10 hover:text-white' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80 group-hover:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z" />
                                <path d="M10 18a3 3 0 01-2.83-2h5.66A3 3 0 0110 18z" />
                            </svg>
                            <span class="flex-1">Notifikasi</span>
                            @if ($notificationBadgeCount > 0)
                                <span class="inline-flex min-w-6 justify-center rounded-full bg-primary-600 px-2 py-0.5 text-[11px] font-bold text-white">{{ $notificationBadgeCount }}</span>
                            @endif
                        </a>
                    @endif
                    </div>
                </div>
                <div class="border-t border-white/10 px-3 py-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="group w-full text-left flex items-center gap-3 rounded-2xl px-4 py-2.5 text-sm font-semibold text-rose-50 hover:bg-rose-500/15 hover:text-white ring-1 ring-transparent hover:ring-rose-400/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-80 group-hover:opacity-100" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h6a1 1 0 110 2H5v10h5a1 1 0 110 2H4a1 1 0 01-1-1V4z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M13.293 7.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L14.586 12H9a1 1 0 110-2h5.586l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            <span class="flex-1">Logout</span>
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <div class="flex h-screen flex-col lg:pl-72">
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
                <div class="flex h-16 items-center justify-between px-4 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 bg-white lg:hidden" @click="sidebarOpen = true">
                            <span class="sr-only">Buka menu</span>
                            <div class="h-0.5 w-5 bg-slate-700"></div>
                            <div class="mt-1 h-0.5 w-5 bg-slate-700"></div>
                            <div class="mt-1 h-0.5 w-5 bg-slate-700"></div>
                        </button>
                        <div>
                            <div class="text-sm font-semibold text-slate-900">@yield('pageTitle', 'Dashboard')</div>
                            <div class="text-xs text-slate-500">@yield('pageSubtitle', 'Sistem Informasi Manajemen Persediaan Produk')</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('notifications.index') }}" class="relative grid h-10 w-10 place-items-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z" />
                                <path d="M10 18a3 3 0 01-2.83-2h5.66A3 3 0 0110 18z" />
                            </svg>
                        @if ($notificationBadgeCount > 0)
                            <span class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-primary-600 px-1 text-[10px] font-bold text-white">{{ $notificationBadgeCount }}</span>
                            @endif
                        </a>
                        <div class="hidden sm:block text-right leading-tight">
                            <div class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-slate-500">{{ auth()->user()->role === 'owner' ? 'Pemilik Toko' : 'Karyawan' }}</div>
                        </div>
                        <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-primary-700 to-primary-600 text-white font-bold">
                            {{ mb_substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto px-4 py-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                        <div class="font-semibold">Terjadi kesalahan:</div>
                        <ul class="mt-1 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
