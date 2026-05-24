@extends('layouts.admin')

@section('title', (auth()->user()->role === 'owner' ? 'Persetujuan Restok' : 'Pengajuan Restok') . ' - ' . config('app.name'))
@section('pageTitle', auth()->user()->role === 'owner' ? 'Restok (Persetujuan)' : 'Pengajuan Restok')
@section('pageSubtitle', auth()->user()->role === 'owner' ? 'Tinjau pengajuan restok dari karyawan' : 'Ajukan restok dan pantau status')

@section('content')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-slate-600">
            Total: <span class="font-semibold text-slate-900">{{ auth()->user()->role === 'owner' ? $batches->total() : $requests->total() }}</span>
        </div>
        @if(auth()->user()->role !== 'owner')
            <a href="{{ route('restock.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                Ajukan Restok
            </a>
        @endif
    </div>

    <div class="mt-4 overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        @if(auth()->user()->role !== 'owner')
                            <th class="px-5 py-3 text-left font-semibold">Pengajuan</th>
                            <th class="px-5 py-3 text-left font-semibold">Tanggal</th>
                            <th class="px-5 py-3 text-left font-semibold">Detail</th>
                            <th class="px-5 py-3 text-left font-semibold">Status</th>
                            <th class="px-5 py-3 text-left font-semibold">Keterangan</th>
                        @else
                            <th class="px-5 py-3 text-left font-semibold">Tanggal</th>
                            <th class="px-5 py-3 text-left font-semibold">Pemohon</th>
                            <th class="px-5 py-3 text-left font-semibold">Item</th>
                            <th class="px-5 py-3 text-left font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @if(auth()->user()->role !== 'owner')
                        @forelse ($requests as $r)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-semibold text-slate-900">{{ str_pad((string) $r->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-5 py-3 text-slate-700">{{ $r->created_at->format('d M Y') }}</td>
                                <td class="px-5 py-3">
                                    <a href="{{ route('restock.show', $r) }}" class="text-sm font-semibold text-primary-700 hover:text-primary-800">detail</a>
                                </td>
                                <td class="px-5 py-3">
                                    @if ($r->status === 'pending')
                                        <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Diajukan</span>
                                    @elseif ($r->status === 'approved')
                                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Selesai</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-slate-700">
                                    @if($r->status === 'pending')
                                        Menunggu
                                    @elseif($r->status === 'approved')
                                        Produk sudah direstok
                                    @else
                                        Ditolak
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-500">Belum ada pengajuan restok.</td>
                            </tr>
                        @endforelse
                    @else
                        @forelse ($batches as $b)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 text-slate-700">{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d H:i:s', $b->batch_time)->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3">
                                    <div class="font-semibold text-slate-900">{{ $b->requester_name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">ID: {{ $b->requested_by }}</div>
                                </td>
                                <td class="px-5 py-3 text-slate-700">
                                    <span class="font-semibold text-slate-900">{{ $b->total_items }}</span> item
                                    @if((int) $b->pending_items > 0)
                                        <span class="ml-2 inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">{{ $b->pending_items }} pending</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if ((int) $b->pending_items > 0)
                                        <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Perlu diproses</span>
                                    @elseif ((int) $b->rejected_items > 0 && (int) $b->approved_items > 0)
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">Campuran</span>
                                    @elseif ((int) $b->rejected_items > 0)
                                        <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">Ditolak</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Disetujui</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('restock.show', $b->first_id) }}" class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            Lihat
                                        </a>
                                        <a href="{{ route('restock.show', $b->first_id) }}?mode=edit" class="rounded-xl bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-700">
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-500">Belum ada pengajuan restok.</td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 px-5 py-4">
            {{ auth()->user()->role === 'owner' ? $batches->links() : $requests->links() }}
        </div>
    </div>
@endsection
