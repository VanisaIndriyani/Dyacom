@extends('layouts.guest')

@section('title', 'Login - ' . config('app.name'))

@section('content')
    <div class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute -top-24 left-1/2 h-72 w-[48rem] -translate-x-1/2 rounded-full bg-primary-200/70 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 left-1/4 h-72 w-[48rem] -translate-x-1/2 rounded-full bg-primary-100 blur-3xl"></div>

        <div class="mx-auto grid min-h-screen max-w-6xl grid-cols-1 items-center gap-8 px-4 py-10 lg:grid-cols-2">
            <div class="relative hidden lg:block">
                <div class="rounded-3xl bg-gradient-to-br from-primary-800 to-primary-600 p-10 text-white shadow-sm">
                    <div class="inline-flex items-center gap-3">
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white/15 text-xl font-bold">D</div>
                        <div>
                            <div class="text-sm font-semibold opacity-90">Percetakan Dyacom</div>
                            <div class="text-xs opacity-80">Manajemen Persediaan Produk</div>
                        </div>
                    </div>

                    <div class="mt-8 text-2xl font-semibold leading-snug">
                        SISTEM INFORMASI MANAJEMEN PERSEDIAAN PRODUK BERBASIS WEB
                    </div>
                    <div class="mt-3 text-sm opacity-90">
                        Desain modern, rapi, responsif, dan mudah digunakan.
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-2xl bg-white/10 p-4">
                            <div class="font-semibold">Pemilik Toko</div>
                            <div class="mt-1 text-xs opacity-90">Kelola akun, persetujuan restok, dan pantau stok menipis.</div>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-4">
                            <div class="font-semibold">Karyawan</div>
                            <div class="mt-1 text-xs opacity-90">Catat stok masuk/keluar dan ajukan restok produk.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full">
                <div class="mx-auto w-full max-w-md">
                    <div class="rounded-3xl bg-white/90 shadow-sm ring-1 ring-slate-200 backdrop-blur overflow-hidden">
                        <div class="px-6 py-6 border-b border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="grid h-11 w-11 place-items-center rounded-2xl bg-primary-600 text-white font-bold">D</div>
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">Login</div>
                                    <div class="text-xs text-slate-500">Masuk untuk melanjutkan</div>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-6">
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
                                    <label class="block text-sm font-medium text-slate-700">Email</label>
                                    <input name="email" type="email" value="{{ old('email') }}" required autofocus class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100" placeholder="contoh@dyacom.test">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Password</label>
                                    <div class="relative mt-1">
                                        <input id="login_password" name="password" type="password" required class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 pr-11 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100" placeholder="••••••••">
                                        <button type="button" data-password-toggle="login_password" class="absolute inset-y-0 right-2 grid w-9 place-items-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700" aria-label="Tampilkan password">
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

                                <div class="flex items-center justify-between">
                                    <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-primary-600 focus:ring-primary-200">
                                        Ingat saya
                                    </label>
                                </div>

                                <button type="submit" class="w-full rounded-xl bg-primary-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-200">
                                    Login
                                </button>
                            </form>

                            <div class="mt-6 rounded-2xl bg-slate-50 px-4 py-3 text-xs text-slate-600">
                                <div class="font-semibold text-slate-700">Akun demo (Seeder)</div>
                                <div class="mt-1">Pemilik: pemilik@dyacom.test / password</div>
                                <div>Karyawan: karyawan@dyacom.test / password</div>
                            </div>
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
