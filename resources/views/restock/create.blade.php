@extends('layouts.admin')

@section('title', 'Ajukan Restok - ' . config('app.name'))
@section('pageTitle', 'Ajukan Restok')
@section('pageSubtitle', 'Karyawan mengajukan restok produk')

@section('content')
    <form method="POST" action="{{ route('restock.store') }}" class="max-w-5xl">
        @csrf

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Produk</label>
                    <select name="product_id" required class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                        <option value="">Pilih produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                {{ $product->name }} - Stok: {{ $product->stock }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Jumlah</label>
                    <input name="quantity" type="number" min="1" value="{{ old('quantity', 1) }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Catatan (opsional)</label>
                    <input name="note" value="{{ old('note') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100" placeholder="Mis: stok hampir habis / order besar">
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
                <a href="{{ route('restock.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Kembali
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                    Kirim Pengajuan
                </button>
            </div>
        </div>
    </form>
@endsection
