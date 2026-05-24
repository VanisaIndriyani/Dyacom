@extends('layouts.admin')

@section('title', 'Data Akun - ' . config('app.name'))
@section('pageTitle', 'Data Akun')
@section('pageSubtitle', 'Kelola akun pemilik dan karyawan')

@section('content')
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="text-sm text-slate-600">
            Total: <span class="font-semibold text-slate-900">{{ $users->total() }}</span>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('users.index') }}" class="w-full sm:w-80">
                <input name="q" value="{{ request('q') }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-100" placeholder="Cari nama / email / role...">
            </form>
            <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                Tambah Akun
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
                        <th class="px-5 py-3 text-left font-semibold">Email</th>
                        <th class="px-5 py-3 text-left font-semibold">Role</th>
                        <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 text-slate-600">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                <div class="text-xs text-slate-500">#{{ $user->id }}</div>
                            </td>
                            <td class="px-5 py-3 text-slate-700">{{ $user->email }}</td>
                            <td class="px-5 py-3">
                                @if ($user->role === 'owner')
                                    <span class="inline-flex rounded-full bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-700">Pemilik</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">Karyawan</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('users.edit', $user) }}" class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus akun ini?')">
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
                            <td colspan="5" class="px-5 py-10 text-center text-slate-500">Belum ada data akun.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
