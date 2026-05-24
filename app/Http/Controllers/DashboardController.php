<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Product;
use App\Models\RestockRequest;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalProduk = Product::count();
        $stokMenipis = Product::query()->whereColumn('stock', '<=', 'min_stock')->count();
        $pendingRestok = RestockRequest::query()->where('status', 'pending')->count();
        $totalSupplier = Supplier::count();

        $startDate = now()->subDays(6)->startOfDay();
        $days = collect(range(6, 0))->map(fn (int $i) => now()->subDays($i)->startOfDay());

        $labels = $days->map(fn (Carbon $d) => $d->format('d/m'))->values();
        $inData = $days->map(fn () => 0)->values();
        $outData = $days->map(fn () => 0)->values();

        $movementRows = StockMovement::query()
            ->selectRaw('DATE(created_at) as d, type, SUM(quantity) as qty')
            ->where('created_at', '>=', $startDate)
            ->groupBy('d', 'type')
            ->get();

        $dateIndex = $days->mapWithKeys(fn (Carbon $d, int $idx) => [$d->toDateString() => $idx]);

        foreach ($movementRows as $row) {
            $idx = $dateIndex[$row->d] ?? null;
            if ($idx === null) {
                continue;
            }
            if ($row->type === 'in') {
                $inData[$idx] = (int) $row->qty;
            } elseif ($row->type === 'out') {
                $outData[$idx] = (int) $row->qty;
            }
        }

        $restockCounts = RestockRequest::query()
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $restockChart = [
            'pending' => (int) ($restockCounts['pending'] ?? 0),
            'approved' => (int) ($restockCounts['approved'] ?? 0),
            'rejected' => (int) ($restockCounts['rejected'] ?? 0),
        ];

        $lowStockProducts = Product::query()
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->limit(5)
            ->get(['name', 'stock', 'min_stock']);

        $lowStockLabels = $lowStockProducts->pluck('name');
        $lowStockStock = $lowStockProducts->pluck('stock');
        $lowStockMin = $lowStockProducts->pluck('min_stock');

        $latestNotifications = AppNotification::query()
            ->where(function ($q) {
                $q->whereNull('user_id')->orWhere('user_id', auth()->id());
            })
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalProduk',
            'stokMenipis',
            'pendingRestok',
            'totalSupplier',
            'labels',
            'inData',
            'outData',
            'restockChart',
            'lowStockLabels',
            'lowStockStock',
            'lowStockMin',
            'latestNotifications',
        ));
    }
}
