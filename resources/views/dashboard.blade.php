@extends('layouts.admin')

@section('title', 'Dashboard - ' . config('app.name'))
@section('pageTitle', 'Dashboard')
@section('pageSubtitle', 'Ringkasan persediaan dan aktivitas')

@section('content')
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="text-sm text-slate-500">Total Produk</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $totalProduk }}</div>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="text-sm text-slate-500">Stok Menipis</div>
            <div class="mt-2 text-2xl font-semibold text-rose-700">{{ $stokMenipis }}</div>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="text-sm text-slate-500">Pengajuan Restok (Pending)</div>
            <div class="mt-2 text-2xl font-semibold text-primary-700">{{ $pendingRestok }}</div>
        </div>
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="text-sm text-slate-500">Total Supplier</div>
            <div class="mt-2 text-2xl font-semibold text-slate-900">{{ $totalSupplier }}</div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Diagram Pergerakan Stok (7 Hari)</div>
                    <div class="text-xs text-slate-500">Total qty stok masuk & stok keluar per hari</div>
                </div>
            </div>
            <div class="mt-4 h-64">
                <canvas id="movementChart" class="h-full w-full"></canvas>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="text-sm font-semibold text-slate-900">Status Pengajuan Restok</div>
            <div class="text-xs text-slate-500">Ringkasan pending/disetujui/ditolak</div>
            <div class="mt-4 h-64">
                <canvas id="restockChart" class="h-full w-full"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 xl:grid-cols-3">
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200 xl:col-span-2">
            <div class="text-sm font-semibold text-slate-900">Produk Stok Menipis (Top 5)</div>
            <div class="text-xs text-slate-500">Perbandingan stok saat ini vs minimum stok</div>
            <div class="mt-4 h-64">
                <canvas id="lowStockChart" class="h-full w-full"></canvas>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Notifikasi Terbaru</div>
                    <div class="text-xs text-slate-500">Update stok menipis & status pengajuan</div>
                </div>
                <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-primary-700 hover:text-primary-800">
                    Lihat semua
                </a>
            </div>

            <div class="mt-4 space-y-3">
                @forelse($latestNotifications as $n)
                    <div class="rounded-2xl border border-slate-200 px-4 py-3 {{ $n->read_at ? 'bg-white' : 'bg-primary-50/40' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-slate-900">{{ $n->title }}</div>
                                @if($n->body)
                                    <div class="mt-1 line-clamp-2 text-xs text-slate-600">{{ $n->body }}</div>
                                @endif
                                <div class="mt-2 text-[11px] text-slate-500">{{ $n->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            @if($n->link)
                                <a href="{{ $n->link }}" class="shrink-0 rounded-xl bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-700">
                                    Buka
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                        Belum ada notifikasi.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Aksi Cepat</div>
                    <div class="text-xs text-slate-500">Navigasi cepat ke menu utama</div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <a href="{{ route('products.create') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-100">
                    Tambah Produk
                </a>
                <a href="{{ route('stock.create') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-100">Catat Stok</a>
                <a href="{{ route('restock.create') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-100">
                    Ajukan Restok
                </a>
                <a href="{{ route('notifications.index') }}" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-100">
                    Lihat Notifikasi
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const labels = @json($labels);
        const inData = @json($inData);
        const outData = @json($outData);
        const restockChart = @json($restockChart);
        const lowStockLabels = @json($lowStockLabels);
        const lowStockStock = @json($lowStockStock);
        const lowStockMin = @json($lowStockMin);

        const textColor = '#334155';
        const gridColor = 'rgba(148, 163, 184, 0.25)';

        new Chart(document.getElementById('movementChart'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Stok Masuk',
                        data: inData,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.12)',
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                    },
                    {
                        label: 'Stok Keluar',
                        data: outData,
                        borderColor: '#e11d48',
                        backgroundColor: 'rgba(225, 29, 72, 0.10)',
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: textColor } },
                },
                scales: {
                    x: { ticks: { color: textColor }, grid: { color: gridColor } },
                    y: { ticks: { color: textColor }, grid: { color: gridColor }, beginAtZero: true },
                },
            },
        });

        new Chart(document.getElementById('restockChart'), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Disetujui', 'Ditolak'],
                datasets: [{
                    data: [restockChart.pending, restockChart.approved, restockChart.rejected],
                    backgroundColor: ['rgba(245, 158, 11, 0.85)', 'rgba(16, 185, 129, 0.85)', 'rgba(225, 29, 72, 0.85)'],
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor } },
                },
                cutout: '68%',
            },
        });

        new Chart(document.getElementById('lowStockChart'), {
            type: 'bar',
            data: {
                labels: lowStockLabels,
                datasets: [
                    {
                        label: 'Stok',
                        data: lowStockStock,
                        backgroundColor: 'rgba(225, 29, 72, 0.75)',
                        borderRadius: 8,
                    },
                    {
                        label: 'Minimum',
                        data: lowStockMin,
                        backgroundColor: 'rgba(37, 99, 235, 0.45)',
                        borderRadius: 8,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor } },
                },
                scales: {
                    x: { ticks: { color: textColor }, grid: { display: false } },
                    y: { ticks: { color: textColor }, grid: { color: gridColor }, beginAtZero: true },
                },
            },
        });
    </script>
@endsection
