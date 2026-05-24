@extends('layouts.admin')

@section('title', (auth()->user()->role === 'owner' ? 'Persetujuan Restok' : 'Detail Restok') . ' - ' . config('app.name'))
@section('pageTitle', auth()->user()->role === 'owner' ? 'Persetujuan Restok' : 'Detail Restok')
@section('pageSubtitle', auth()->user()->role === 'owner' ? (($mode ?? '') === 'edit' ? 'Ubah keterangan untuk karyawan' : 'Pilih produk yang akan diproses') : 'Informasi pengajuan restok')

@section('content')
    <div class="max-w-5xl">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-slate-600">
                @if(auth()->user()->role === 'owner')
                    Pengajuan dari <span class="font-semibold text-slate-900">{{ $restock->requester?->name ?? '-' }}</span>
                    <span class="mx-2 text-slate-300">•</span>
                    <span class="font-semibold text-slate-900">{{ $restock->created_at->format('d/m/Y H:i') }}</span>
                @else
                    Pengajuan: <span class="font-semibold text-slate-900">{{ str_pad((string) $restock->id, 5, '0', STR_PAD_LEFT) }}</span>
                @endif
            </div>
            <a href="{{ route('restock.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Kembali
            </a>
        </div>

        @if(auth()->user()->role === 'owner')
            @php
                $pendingCount = (int) ($batchRequests?->where('status', 'pending')->count() ?? 0);
                $defaultDecisionNote = (string) ($batchRequests?->pluck('decision_note')->filter()->first() ?? '');
            @endphp

            <div class="mt-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-slate-600">
                        Total <span class="font-semibold text-slate-900">{{ $batchRequests->count() }}</span> item
                        @if($pendingCount > 0)
                            <span class="ml-2 inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">{{ $pendingCount }} pending</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('restock.show', $restock) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Lihat
                        </a>
                        <a href="{{ route('restock.show', $restock) }}?mode=edit" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">
                            Edit Keterangan
                        </a>
                    </div>
                </div>

                <div class="mt-5 overflow-hidden rounded-2xl ring-1 ring-slate-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold w-12">Pilih</th>
                                    <th class="px-4 py-3 text-left font-semibold">Produk</th>
                                    <th class="px-4 py-3 text-left font-semibold">Qty</th>
                                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                                    <th class="px-4 py-3 text-left font-semibold">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($batchRequests as $r)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3">
                                            @if(($mode ?? '') !== 'edit')
                                                <input type="checkbox" name="restock_ids[]" value="{{ $r->id }}" form="bulk-decision-form"
                                                    class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600"
                                                    {{ $r->status !== 'pending' ? 'disabled' : '' }}>
                                            @else
                                                <span class="text-xs text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-semibold text-slate-900">{{ $r->product?->name ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $r->quantity }}</td>
                                        <td class="px-4 py-3">
                                            @if ($r->status === 'pending')
                                                <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Pending</span>
                                            @elseif ($r->status === 'approved')
                                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Disetujui</span>
                                            @else
                                                <span class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">Ditolak</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-slate-700">
                                            {{ $r->decision_note ?: '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                                @if($batchRequests->isEmpty())
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">Data tidak ditemukan.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(($mode ?? '') === 'edit')
                    <form id="bulk-note-form" method="POST" action="{{ route('restock.bulkNoteUpdate') }}" class="mt-5 space-y-3">
                        @csrf
                        <input type="hidden" name="batch_requested_by" value="{{ $restock->requested_by }}">
                        <input type="hidden" name="batch_time" value="{{ $batchTime }}">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Keterangan (akan dikirim ke karyawan)</label>
                            <textarea name="decision_note" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none ring-0 focus:border-primary-300 focus:ring-4 focus:ring-primary-100">{{ old('decision_note', $defaultDecisionNote) }}</textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">
                                Simpan Keterangan
                            </button>
                        </div>
                    </form>
                @else
                    <form id="bulk-decision-form" method="POST" action="{{ route('restock.bulkDecide') }}" class="mt-5 space-y-3">
                        @csrf
                        <input type="hidden" name="batch_requested_by" value="{{ $restock->requested_by }}">
                        <input type="hidden" name="batch_time" value="{{ $batchTime }}">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Keterangan (opsional)</label>
                            <textarea name="decision_note" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none ring-0 focus:border-primary-300 focus:ring-4 focus:ring-primary-100">{{ old('decision_note') }}</textarea>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                            <button type="submit" name="action" value="approve" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700" onclick="return confirm('Setujui item terpilih?')">
                                Setujui Terpilih
                            </button>
                            <button type="submit" name="action" value="reject" class="inline-flex items-center justify-center rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-rose-700" onclick="return confirm('Tolak item terpilih?')">
                                Tolak Terpilih
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        @else
            <div class="mt-4 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <div class="text-xs font-semibold text-slate-500">Produk</div>
                        <div class="mt-1 text-sm font-semibold text-slate-900">{{ $restock->product?->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-500">Jumlah</div>
                        <div class="mt-1 text-sm font-semibold text-slate-900">{{ $restock->quantity }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-500">Tanggal</div>
                        <div class="mt-1 text-sm font-semibold text-slate-900">{{ $restock->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-500">Status</div>
                        <div class="mt-1">
                            @if ($restock->status === 'pending')
                                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Pending</span>
                            @elseif ($restock->status === 'approved')
                                <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Disetujui</span>
                            @else
                                <span class="inline-flex rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">Ditolak</span>
                            @endif
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-xs font-semibold text-slate-500">Keterangan</div>
                        <div class="mt-1 text-sm font-semibold text-slate-900">{{ $restock->decision_note ?: '-' }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
