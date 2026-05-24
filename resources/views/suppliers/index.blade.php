@extends('layouts.admin')

@section('title', 'Data Supplier - ' . config('app.name'))
@section('pageTitle', 'Data Supplier')
@section('pageSubtitle', 'Kelola supplier percetakan')

@section('content')
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="text-sm text-slate-600">
            Total: <span class="font-semibold text-slate-900">{{ $suppliers->total() }}</span>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('suppliers.index') }}" class="w-full sm:w-80">
                <input name="q" value="{{ request('q') }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100" placeholder="Cari nama / telp / email...">
            </form>
            <a href="{{ route('suppliers.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                Tambah Supplier
            </a>
        </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-5 py-3 text-left font-semibold">No</th>
                        <th class="px-5 py-3 text-left font-semibold">Nama</th>
                        <th class="px-5 py-3 text-left font-semibold">Kontak</th>
                        <th class="px-5 py-3 text-left font-semibold">Alamat</th>
                        <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($suppliers as $supplier)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 text-slate-600">
                                {{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-semibold text-slate-900">{{ $supplier->name }}</div>
                                <div class="text-xs text-slate-500">#{{ $supplier->id }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="text-slate-800">{{ $supplier->phone ?? '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $supplier->email ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3 text-slate-700">
                                {{ $supplier->address ?? '-' }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Hapus supplier ini?')">
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
                            <td colspan="5" class="px-5 py-10 text-center text-slate-500">Belum ada data supplier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">
            {{ $suppliers->links() }}
        </div>
    </div>
@endsection
