<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Product;
use App\Models\RestockRequest;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $totalProduk = Product::count();
        $stokMenipis = Product::query()->whereColumn('stock', '<=', 'min_stock')->count();

        $startInput = $request->query('start_date');
        $endInput = $request->query('end_date');

        $endDate = $endInput ? Carbon::parse($endInput)->endOfDay() : now()->endOfDay();
        $startDate = $startInput ? Carbon::parse($startInput)->startOfDay() : $endDate->copy()->subDays(6)->startOfDay();

        if ($startDate->greaterThan($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        $maxDays = 31;
        if ($startDate->diffInDays($endDate) + 1 > $maxDays) {
            $startDate = $endDate->copy()->subDays($maxDays - 1)->startOfDay();
        }

        $days = collect();
        $cursor = $startDate->copy()->startOfDay();
        while ($cursor->lessThanOrEqualTo($endDate->copy()->startOfDay())) {
            $days->push($cursor->copy());
            $cursor->addDay();
        }

        $labels = $days->map(fn (Carbon $d) => $d->format('d/m'))->values();
        $inData = $days->map(fn () => 0)->values();
        $outData = $days->map(fn () => 0)->values();

        $movementRows = StockMovement::query()
            ->selectRaw('DATE(created_at) as d, type, SUM(quantity) as qty')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('d', 'type')
            ->get();

        $dateIndex = $days->mapWithKeys(fn (Carbon $d, int $idx) => [$d->toDateString() => $idx]);

        $totalStockIn = 0;
        $totalStockOut = 0;

        foreach ($movementRows as $row) {
            $idx = $dateIndex[$row->d] ?? null;
            if ($idx === null) {
                continue;
            }
            if ($row->type === 'in') {
                $inData[$idx] = (int) $row->qty;
                $totalStockIn += (int) $row->qty;
            } elseif ($row->type === 'out') {
                $outData[$idx] = (int) $row->qty;
                $totalStockOut += (int) $row->qty;
            }
        }

        $restockCounts = RestockRequest::query()
            ->selectRaw('status, COUNT(*) as c')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->pluck('c', 'status');

        $restockChart = [
            'pending' => (int) ($restockCounts['pending'] ?? 0),
            'approved' => (int) ($restockCounts['approved'] ?? 0),
            'rejected' => (int) ($restockCounts['rejected'] ?? 0),
        ];

        $pendingRestok = $restockChart['pending'];
        $totalSupplier = Supplier::count();

        $lowStockProducts = Product::query()
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->limit(5)
            ->get(['name', 'unit', 'stock', 'min_stock']);

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
            'totalStockIn',
            'totalStockOut',
            'startDate',
            'endDate',
            'labels',
            'inData',
            'outData',
            'restockChart',
            'lowStockProducts',
            'latestNotifications',
        ));
    }
}
