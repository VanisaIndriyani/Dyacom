@extends('layouts.admin')

@section('title', 'Data Supplier - ' . config('app.name'))
@section('pageTitle', 'Supplier')
@section('pageSubtitle', 'Kelola data supplier')

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

    <div class="mt-4 space-y-3">
        @forelse ($suppliers as $supplier)
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="text-sm font-semibold text-slate-900">{{ $supplier->name }}</div>
                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                #{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}
                            </span>
                        </div>

                        <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-slate-700 sm:grid-cols-2">
                            <div class="flex items-center gap-2">
                                <span class="grid h-7 w-7 place-items-center rounded-xl bg-slate-100 text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3.5A1.5 1.5 0 013.5 2h1A1.5 1.5 0 016 3.5V5a1 1 0 01-.293.707L5 6.414a12.042 12.042 0 005.586 5.586l.707-.707A1 1 0 0112.999 11H14.5A1.5 1.5 0 0116 12.5v1a1.5 1.5 0 01-1.5 1.5H14c-6.627 0-12-5.373-12-12v-.5z" />
                                    </svg>
                                </span>
                                <div class="truncate">{{ $supplier->phone ?? '-' }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="grid h-7 w-7 place-items-center rounded-xl bg-slate-100 text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </span>
                                <div class="truncate">{{ $supplier->email ?? '-' }}</div>
                            </div>
                            <div class="flex items-start gap-2 sm:col-span-2">
                                <span class="mt-0.5 grid h-7 w-7 shrink-0 place-items-center rounded-xl bg-slate-100 text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <div class="text-slate-700">{{ $supplier->address ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="grid h-10 w-10 place-items-center rounded-2xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                <path fill-rule="evenodd" d="M2 16a2 2 0 002 2h12a1 1 0 100-2H4V4a1 1 0 10-2 0v12z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Hapus supplier ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="grid h-10 w-10 place-items-center rounded-2xl border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6 3a1 1 0 011-1h6a1 1 0 011 1v1h3a1 1 0 110 2h-1v10a2 2 0 01-2 2H6a2 2 0 01-2-2V7H3a1 1 0 110-2h3V3zm2 4a1 1 0 10-2 0v8a1 1 0 102 0V7zm4-1a1 1 0 00-1 1v8a1 1 0 102 0V7a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-3xl bg-white px-6 py-10 text-center text-sm text-slate-500 shadow-sm ring-1 ring-slate-200">
                Belum ada data supplier.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>
@endsection
