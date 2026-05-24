@extends('layouts.admin')

@section('title', 'Catat Stok - ' . config('app.name'))
@section('pageTitle', 'Catat Stok')
@section('pageSubtitle', 'Input stok masuk dan stok keluar')

@section('content')
    <form method="POST" action="{{ route('stock.store') }}" class="max-w-5xl">
        @csrf

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Produk</label>
                    <select name="product_id" required class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                        <option value="">Pilih produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                {{ $product->name }} (SKU: {{ $product->sku }}) - Stok: {{ $product->stock }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Tipe</label>
                    <select name="type" required class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                        <option value="in" @selected(old('type') === 'in')>Stok Masuk</option>
                        <option value="out" @selected(old('type') === 'out')>Stok Keluar</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Jumlah</label>
                    <input name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Supplier (opsional, untuk stok masuk)</label>
                    <select name="supplier_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                        <option value="">-</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Catatan (opsional)</label>
                    <textarea name="note" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">{{ old('note') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
                <a href="{{ route('stock.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Kembali
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                    Simpan
                </button>
            </div>
        </div>
    </form>
@endsection

