<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Product;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $lowStockCount = 0;
        $lowStockProducts = collect();

        if ($request->user()->role !== 'owner') {
            $lowStockQuery = Product::query()->whereColumn('stock', '<=', 'min_stock');
            $lowStockCount = (clone $lowStockQuery)->count();
            $lowStockProducts = $lowStockQuery
                ->orderBy('stock')
                ->orderBy('name')
                ->limit(20)
                ->get();
        }

        $notificationsQuery = AppNotification::query();

        if ($request->user()->role === 'owner') {
            $notificationsQuery
                ->where('user_id', $request->user()->id)
                ->where('type', 'restock_request');
        } else {
            $notificationsQuery->where(function ($q) use ($request) {
                $q->whereNull('user_id')->orWhere('user_id', $request->user()->id);
            });
        }

        $notifications = $notificationsQuery
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function ($q2) use ($like) {
                    $q2->where('title', 'like', $like)
                        ->orWhere('body', 'like', $like)
                        ->orWhere('type', 'like', $like);
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('notifications.index', compact('notifications', 'lowStockCount', 'lowStockProducts'));
    }

    public function markRead(Request $request, AppNotification $notification)
    {
        if ($notification->user_id !== null && $notification->user_id !== $request->user()->id) {
            return redirect()->route('notifications.index')->with('error', 'Anda tidak memiliki akses ke notifikasi ini.');
        }

        $notification->update(['read_at' => now()]);

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllRead(Request $request)
    {
        AppNotification::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
