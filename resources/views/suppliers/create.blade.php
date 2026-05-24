@extends('layouts.admin')

@section('title', 'Tambah Supplier - ' . config('app.name'))
@section('pageTitle', 'Tambah Supplier')
@section('pageSubtitle', 'Input data supplier baru')

@section('content')
    <form method="POST" action="{{ route('suppliers.store') }}" class="max-w-4xl">
        @csrf
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Nama Supplier</label>
                    <input name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">No. Telepon</label>
                    <input name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input name="email" type="email" value="{{ old('email') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700">Alamat</label>
                    <textarea name="address" rows="3" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">{{ old('address') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
                <a href="{{ route('suppliers.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                    Simpan
                </button>
            </div>
        </div>
    </form>
@endsection

