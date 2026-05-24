@extends('layouts.admin')

@section('title', 'Notifikasi - ' . config('app.name'))
@section('pageTitle', 'Notifikasi')
@section('pageSubtitle', 'Stok menipis dan status pengajuan restok')

@section('content')
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

