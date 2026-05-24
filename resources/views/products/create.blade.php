@extends('layouts.admin')

@section('title', 'Tambah Produk - ' . config('app.name'))
@section('pageTitle', 'Tambah Produk')
@section('pageSubtitle', 'Input data produk baru')

@section('content')
    <form method="POST" action="{{ route('products.store') }}" class="max-w-5xl">
        @csrf

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Produk</label>
                    <input name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Supplier (opsional)</label>
                    <select name="supplier_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                        <option value="">-</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Satuan (opsional)</label>
                    <input name="unit" value="{{ old('unit') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100" placeholder="pcs / rim / box">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Stok Awal</label>
                    <input name="stock" type="number" min="0" value="{{ old('stock', 0) }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Minimum Stok</label>
                    <input name="min_stock" type="number" min="0" value="{{ old('min_stock', 0) }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Harga Jual</label>
                    <input name="price" type="number" min="0" step="0.01" value="{{ old('price', 0) }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Deskripsi (opsional)</label>
                    <textarea name="description" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                    Simpan
                </button>
            </div>
        </div>
    </form>
@endsection
