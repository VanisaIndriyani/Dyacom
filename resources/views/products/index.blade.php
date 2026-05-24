@extends('layouts.admin')

@section('title', 'Data Produk - ' . config('app.name'))
@section('pageTitle', 'Data Produk')
@section('pageSubtitle', 'Kelola produk dan persediaan')

@section('content')
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="text-sm text-slate-600">
            Total: <span class="font-semibold text-slate-900">{{ $products->total() }}</span>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('products.index') }}" class="w-full sm:w-80">
                <input name="q" value="{{ request('q') }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100" placeholder="Cari nama / supplier...">
            </form>
            <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                Tambah Produk
            </a>
        </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold">No</th>
                        <th class="px-5 py-3 text-left font-semibold">Produk</th>
                        <th class="px-5 py-3 text-left font-semibold">Supplier</th>
                        <th class="px-5 py-3 text-left font-semibold">Stok</th>
                        <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($products as $product)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 text-slate-600">
                                {{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-semibold text-slate-900">{{ $product->name }}</div>
                            </td>
                            <td class="px-5 py-3 text-slate-700">
                                {{ $product->supplier?->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="inline-flex items-center gap-2">
                                    <span class="font-semibold {{ $product->is_low_stock ? 'text-rose-700' : 'text-slate-900' }}">
                                        {{ $product->stock }}
                                    </span>
                                    <span class="text-xs text-slate-500">min {{ $product->min_stock }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('products.edit', $product) }}" class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-slate-500">Belum ada data produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">
            {{ $products->links() }}
        </div>
    </div>
@endsection
