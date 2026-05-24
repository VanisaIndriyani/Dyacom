@extends('layouts.admin')

@section('title', 'Detail Restok - ' . config('app.name'))
@section('pageTitle', 'Detail Restok')
@section('pageSubtitle', 'Informasi pengajuan restok')

@section('content')
    <div class="max-w-4xl">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-slate-600">
                Pengajuan: <span class="font-semibold text-slate-900">{{ str_pad((string) $restock->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <a href="{{ route('restock.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Kembali
            </a>
        </div>

        <div class="mt-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <div class="text-xs font-semibold text-slate-500">Produk</div>
                    <div class="mt-1 text-sm font-semibold text-slate-900">{{ $restock->product?->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500">Jumlah</div>
                    <div class="mt-1 text-sm font-semibold text-slate-900">{{ $restock->quantity }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500">Tanggal</div>
                    <div class="mt-1 text-sm font-semibold text-slate-900">{{ $restock->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-500">Status</div>
                    <div class="mt-1">
                        @if ($restock->status === 'pending')
                            <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Pending</span>
                        @elseif ($restock->status === 'approved')
                            <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Disetujui</span>
                        @else
                            <span class="inline-flex rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">Ditolak</span>
                        @endif
                    </div>
                </div>
                <div class="md:col-span-2">
                    <div class="text-xs font-semibold text-slate-500">Pemohon</div>
                    <div class="mt-1 text-sm font-semibold text-slate-900">{{ $restock->requester?->name ?? '-' }}</div>
                </div>
            </div>

            @if(auth()->user()->role === 'owner' && $restock->status === 'pending')
                <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
                    <form method="POST" action="{{ route('restock.approve', $restock) }}" onsubmit="return confirm('Setujui pengajuan ini?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                            Setujui
                        </button>
                    </form>
                    <form method="POST" action="{{ route('restock.reject', $restock) }}" onsubmit="return confirm('Tolak pengajuan ini?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-700">
                            Tolak
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection

