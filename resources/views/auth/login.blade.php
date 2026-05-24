@extends('layouts.guest')

@section('title', 'Login - ' . config('app.name'))

@section('content')
    <div class="min-h-screen bg-slate-100">
        <div class="grid min-h-screen grid-cols-1 lg:grid-cols-2">
            <div class="relative hidden overflow-hidden bg-primary-400 lg:block">
                <div class="absolute -left-24 top-16 h-64 w-64 rounded-full bg-white/20"></div>
                <div class="absolute left-10 top-24 h-40 w-40 rounded-full bg-white/10"></div>
                <div class="absolute bottom-16 left-16 h-24 w-24 rounded-2xl bg-white/15"></div>
                <div class="absolute bottom-10 right-16 h-20 w-20 rounded-2xl bg-white/10"></div>

                <div class="relative flex h-full items-center px-16">
                    <div class="max-w-lg text-white">
                        <div class="inline-flex items-center gap-3 rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold shadow-sm ring-1 ring-white/20">
                            PERCETAKAN DYACOM
                        </div>
                        <div class="mt-3 inline-flex items-center rounded-lg bg-white/10 px-4 py-2 text-sm font-semibold ring-1 ring-white/15">
                            Manajemen Persediaan Produk
                        </div>

                        <div class="mt-6 text-4xl font-extrabold leading-tight drop-shadow-sm">
                            Sistem Manajemen Stok Modern
                        </div>
                        <div class="mt-3 text-sm text-white/90">
                            Mudah, cepat, dan terpercaya untuk kebutuhan persediaan produk percetakan.
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative flex items-center justify-center px-4 py-10">
                <div class="w-full max-w-md">
                    <div class="mb-6 text-center lg:hidden">
                        <div class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white">
                            Percetakan Dyacom
                        </div>
                        <div class="mt-2 text-xs text-slate-600">Manajemen Persediaan Produk</div>
                    </div>

                    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                        <div class="px-6 py-6 text-center">
                            <div class="text-lg font-bold text-slate-900">Selamat Datang</div>
                            <div class="mt-1 text-xs text-slate-500">Silakan masuk ke akun Anda</div>
                        </div>

                        <div class="px-6 pb-6">
                            @if ($errors->any())
                                <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                                    <div class="font-semibold">Terjadi kesalahan:</div>
                                    <ul class="mt-1 list-disc pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700">Email atau Username</label>
                                    <input name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100" placeholder="contoh@dyacom.test">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700">Password</label>
                                    <div class="relative mt-1">
                                        <input id="login_password" name="password" type="password" required class="w-full rounded-md border border-slate-300 bg-white px-3 py-2 pr-11 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100" placeholder="••••••••">
                                        <button type="button" data-password-toggle="login_password" class="absolute inset-y-0 right-0 grid w-10 place-items-center text-slate-500 hover:text-slate-700" aria-label="Tampilkan password">
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

                                <label class="inline-flex items-center gap-2 text-xs text-slate-600">
                                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-primary-600 focus:ring-primary-200">
                                    Ingat saya
                                </label>

                                <button type="submit" class="w-full rounded-md bg-primary-400 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-500 focus:outline-none focus:ring-4 focus:ring-primary-100">
                                    Masuk
                                </button>

                                <div class="pt-2 text-center text-xs text-slate-500">
                                    Belum punya akun?
                                    <a href="#" onclick="return false" class="font-semibold text-primary-600 hover:text-primary-700">Daftar</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="mt-4 text-center text-xs text-slate-500">
                        {{ now()->format('Y') }} © Percetakan Dyacom
                    </div>
                </div>
            </div>
        </div>
    </div>

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
