@extends('layouts.admin')

@section('title', 'Catat Stok - ' . config('app.name'))
@section('pageTitle', 'Catat Stok')
@section('pageSubtitle', 'Riwayat stok masuk dan stok keluar')

@section('content')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-slate-600">
            Total: <span class="font-semibold text-slate-900">{{ $movements->total() }}</span>
        </div>
        <a href="{{ route('stock.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
            Catat Stok
        </a>
    </div>

    <div class="mt-4 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold">Tanggal</th>
                        <th class="px-5 py-3 text-left font-semibold">Produk</th>
                        <th class="px-5 py-3 text-left font-semibold">Tipe</th>
                        <th class="px-5 py-3 text-left font-semibold">Qty</th>
                        <th class="px-5 py-3 text-left font-semibold">Petugas</th>
                        <th class="px-5 py-3 text-left font-semibold">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($movements as $m)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 text-slate-700">
                                {{ $m->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-semibold text-slate-900">{{ $m->product?->name ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3">
                                @if ($m->type === 'in')
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Masuk</span>
                                @else
                                    <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">Keluar</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-semibold text-slate-900">{{ $m->quantity }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ $m->user?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-slate-700">
                                <div>{{ $m->note ?? '-' }}</div>
                                @if($m->supplier)
                                    <div class="text-xs text-slate-500">Supplier: {{ $m->supplier->name }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-slate-500">Belum ada riwayat stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">
            {{ $movements->links() }}
        </div>
    </div>
@endsection
