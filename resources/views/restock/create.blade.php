@extends('layouts.admin')

@section('title', 'Ajukan Restok - ' . config('app.name'))
@section('pageTitle', 'Ajukan Restok')
@section('pageSubtitle', 'Pilih produk menipis dan tentukan jumlah restok')

@section('content')
    <form method="POST" action="{{ route('restock.store') }}" class="max-w-5xl">
        @csrf

        <div x-data="{ selected: @js(array_map('strval', old('products', []))) }" class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Daftar Produk Menipis</div>
                    <div class="mt-1 text-xs text-slate-500">Centang produk yang ingin diajukan restok, lalu isi jumlahnya</div>
                </div>
                <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">{{ $products->count() }} Produk</span>
            </div>

            <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200">
                @forelse ($products as $product)
                    @php
                        $suggested = max((int) $product->min_stock - (int) $product->stock, 1);
                        $oldQty = old('quantities.'.$product->id, $suggested);
                    @endphp
                    <div class="flex flex-col gap-3 border-b border-slate-200 bg-white px-4 py-3 last:border-b-0 sm:flex-row sm:items-center sm:justify-between">
                        <label class="flex min-w-0 items-start gap-3 sm:items-center">
                            <input type="checkbox" name="products[]" value="{{ $product->id }}" x-model="selected" class="mt-1 h-5 w-5 rounded border-slate-300 text-primary-600 focus:ring-primary-200 sm:mt-0">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-slate-900">{{ $product->name }}</div>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-600">
                                    <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 font-semibold text-rose-700">
                                        Jumlah: {{ $product->stock }}{{ $product->unit ? ' '.$product->unit : '' }}
                                    </span>
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700">
                                        Min: {{ $product->min_stock }}
                                    </span>
                                </div>
                            </div>
                        </label>

                        <div class="flex items-center gap-2 sm:justify-end">
                            <div class="w-full sm:w-44">
                                <label class="block text-[11px] font-semibold text-slate-600">Jumlah Restok</label>
                                <input
                                    name="quantities[{{ $product->id }}]"
                                    type="number"
                                    min="1"
                                    value="{{ $oldQty }}"
                                    :disabled="!selected.includes('{{ (string) $product->id }}')"
                                    class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100 disabled:bg-slate-50 disabled:text-slate-400"
                                >
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">
                        Tidak ada produk menipis saat ini.
                    </div>
                @endforelse
            </div>

            <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
                <a href="{{ route('restock.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Kembali
                </a>
                <button type="submit" :disabled="selected.length === 0" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                    Kirim Pengajuan
                </button>
            </div>
        </div>
    </form>
@endsection
