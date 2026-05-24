@extends('layouts.admin')

@section('title', 'Notifikasi - ' . config('app.name'))
@section('pageTitle', 'Notifikasi')
@section('pageSubtitle', auth()->user()->role === 'owner' ? 'Pengajuan restok masuk' : 'Stok menipis dan status pengajuan restok')

@section('content')
    @if(auth()->user()->role !== 'owner')
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <div class="text-sm font-semibold text-slate-900">Notifikasi Stok</div>
                        <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">{{ $lowStockCount }} Produk</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-500">Produk yang stoknya <= minimum</div>
                </div>
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">
                    Lihat Semua Produk
                </a>
            </div>

            <div class="mt-4 max-h-72 overflow-y-auto pr-1">
                <div class="space-y-3">
                    @forelse($lowStockProducts as $p)
                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-xl bg-amber-50 text-amber-700 ring-1 ring-amber-200/60">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l6.518 11.59C19.02 16.06 18.044 17.75 16.518 17.75H3.482c-1.526 0-2.502-1.69-1.742-3.061l6.517-11.59zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-7a1 1 0 00-1 1v3a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        <div class="truncate text-sm font-semibold text-slate-900">{{ $p->name }}</div>
                                    </div>
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-600">
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700">{{ $p->category ?? '-' }}</span>
                                        <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 font-semibold text-rose-700">
                                            Jumlah: {{ $p->stock }}{{ $p->unit ? ' '.$p->unit : '' }}
                                        </span>
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700">
                                            Min: {{ $p->min_stock }}
                                        </span>
                                    </div>
                                </div>

                                <a href="{{ route('products.edit', $p) }}" class="shrink-0 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                            Semua produk aman.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-slate-600">
            Total: <span class="font-semibold text-slate-900">{{ $notifications->total() }}</span>
        </div>
        <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button type="submit" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Tandai Semua Dibaca
            </button>
        </form>
    </div>

    <div class="mt-4 space-y-3">
        @forelse ($notifications as $n)
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 {{ $n->read_at ? 'ring-slate-200' : 'ring-primary-200' }}">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <div class="text-sm font-semibold text-slate-900">{{ $n->title }}</div>
                            @if(! $n->read_at)
                                <span class="inline-flex rounded-full bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-700">Baru</span>
                            @endif
                        </div>
                        @if($n->body)
                            <div class="mt-1 text-sm text-slate-600">{{ $n->body }}</div>
                        @endif
                        <div class="mt-2 text-xs text-slate-500">{{ $n->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        @if($n->link)
                            <a href="{{ $n->link }}" class="rounded-xl bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-700">
                                Buka
                            </a>
                        @endif

                        @if(! $n->read_at)
                            <form method="POST" action="{{ route('notifications.read', $n) }}">
                                @csrf
                                <button type="submit" class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Tandai Dibaca
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-3xl bg-white px-6 py-10 text-center text-sm text-slate-500 shadow-sm ring-1 ring-slate-200">
                Belum ada notifikasi.
            </div>
        @endforelse

        <div class="pt-2">
            {{ $notifications->links() }}
        </div>
    </div>
@endsection
