@extends('layouts.admin')

@section('title', 'Edit Akun - ' . config('app.name'))
@section('pageTitle', 'Edit Akun')
@section('pageSubtitle', 'Perbarui data akun')

@section('content')
    <form method="POST" action="{{ route('users.update', $user) }}" class="max-w-5xl">
        @csrf
        @method('PUT')

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama</label>
                    <input name="name" value="{{ old('name', $user->name) }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Role</label>
                    <select name="role" required class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                        <option value="employee" @selected(old('role', $user->role) === 'employee')>Karyawan</option>
                        <option value="owner" @selected(old('role', $user->role) === 'owner')>Pemilik Toko</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Password (kosongkan jika tidak diubah)</label>
                    <div class="relative mt-1">
                        <input id="user_password_edit" name="password" type="password" class="w-full rounded-xl border border-slate-200 px-3 py-2 pr-11 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100">
                        <button type="button" data-password-toggle="user_password_edit" class="absolute inset-y-0 right-2 grid w-9 place-items-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700" aria-label="Tampilkan password">
                            <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 3c-5 0-9 4-9 7s4 7 9 7 9-4 9-7-4-7-9-7zm0 12a5 5 0 110-10 5 5 0 010 10z" />
                                <path d="M10 7a3 3 0 100 6 3 3 0 000-6z" />
                            </svg>
                            <svg data-eye-off xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3.28 2.22a.75.75 0 10-1.06 1.06l1.62 1.62C2.08 6.38 1 8.31 1 10c0 3 4 7 9 7 1.73 0 3.34-.48 4.73-1.22l1.99 1.99a.75.75 0 101.06-1.06L3.28 2.22zM10 15.5c-3.95 0-7.5-3.46-7.5-5.5 0-1.23.82-2.83 2.23-4.14l2.01 2.01A3.99 3.99 0 006 10a4 4 0 005.13 3.83l2.01 2.01c-1 .43-2.07.66-3.14.66z" />
                                <path d="M10 4.5c3.95 0 7.5 3.46 7.5 5.5 0 1.13-.7 2.56-1.92 3.8l-2.05-2.05A3.99 3.99 0 0014 10a4 4 0 00-5.18-3.84L6.77 4.11c1-.39 2.07-.61 3.23-.61z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-end">
                <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Kembali
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-password-toggle');
                const input = document.getElementById(id);
                if (!input) return;
                const open = btn.querySelector('[data-eye-open]');
                const off = btn.querySelector('[data-eye-off]');
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                if (open && off) {
                    open.classList.toggle('hidden', !isHidden);
                    off.classList.toggle('hidden', isHidden);
                }
                btn.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
            });
        });
    </script>
@endsection
