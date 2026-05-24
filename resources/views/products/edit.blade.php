@extends('layouts.admin')

@section('title', 'Edit Produk - ' . config('app.name'))
@section('pageTitle', 'Edit Produk')
@section('pageSubtitle', 'Perbarui data produk')

@section('content')
    <form method="POST" action="{{ route('products.update', $product) }}" class="max-w-5xl">
        @csrf
        @method('PUT')

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Produk</label>
                    <input name="name" value="{{ old('name', $product->name) }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Kategori</label>
                    <input name="category" value="{{ old('category', $product->category) }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Supplier (opsional)</label>
                    <select name="supplier_id" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                        <option value="">-</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Satuan (opsional)</label>
                    <input name="unit" value="{{ old('unit', $product->unit) }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Stok Saat Ini</label>
                    <input value="{{ $product->stock }}" disabled class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Minimum Stok</label>
                    <input name="min_stock" type="number" min="0" value="{{ old('min_stock', $product->min_stock) }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Harga Jual</label>
                    <input name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price) }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Status Stok</label>
                    <div class="mt-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                        @if($product->is_low_stock)
                            <span class="font-semibold text-rose-700">Menipis</span>
                        @else
                            <span class="font-semibold text-emerald-700">Aman</span>
                        @endif
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Deskripsi (opsional)</label>
                    <textarea name="description" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Kembali
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
@endsection
