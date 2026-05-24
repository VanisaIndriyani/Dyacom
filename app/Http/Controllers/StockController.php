<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $movements = StockMovement::query()
            ->with(['product', 'supplier', 'user'])
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function ($q2) use ($like) {
                    $q2->where('type', 'like', $like)
                        ->orWhere('note', 'like', $like)
                        ->orWhereHas('product', fn ($qp) => $qp->where('name', 'like', $like)->orWhere('sku', 'like', $like))
                        ->orWhereHas('supplier', fn ($qs) => $qs->where('name', 'like', $like))
                        ->orWhereHas('user', fn ($qu) => $qu->where('name', 'like', $like)->orWhere('email', 'like', $like));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('stock.index', compact('movements'));
    }

    public function create()
    {
        $products = Product::query()->orderBy('name')->get();
        $suppliers = Supplier::query()->orderBy('name')->get();

        return view('stock.create', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'note' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        DB::transaction(function () use ($data, $user) {
            $product = Product::query()->lockForUpdate()->findOrFail($data['product_id']);

            if ($data['type'] === 'out' && $data['quantity'] > $product->stock) {
                abort(422, 'Stok tidak mencukupi untuk stok keluar.');
            }

            $newStock = $data['type'] === 'in'
                ? $product->stock + $data['quantity']
                : $product->stock - $data['quantity'];

            $product->update(['stock' => $newStock]);

            StockMovement::create([
                'product_id' => $product->id,
                'supplier_id' => $data['type'] === 'in' ? ($data['supplier_id'] ?? null) : null,
                'user_id' => $user->id,
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? null,
            ]);

            if ($product->fresh()->stock <= $product->min_stock) {
                $this->createLowStockNotificationsIfNeeded($product->fresh());
            }
        });

        return redirect()->route('stock.index')->with('success', 'Stok berhasil dicatat.');
    }

    private function createLowStockNotificationsIfNeeded(Product $product): void
    {
        if ($product->stock > $product->min_stock) {
            return;
        }

        $link = route('products.edit', $product);
        $title = 'Stok menipis: '.$product->name;
        $body = 'SKU: '.$product->sku.' | Stok: '.$product->stock.' | Minimum: '.$product->min_stock;

        $owners = User::query()->where('role', 'owner')->get();

        foreach ($owners as $owner) {
            $exists = AppNotification::query()
                ->where('user_id', $owner->id)
                ->where('type', 'low_stock')
                ->where('link', $link)
                ->whereNull('read_at')
                ->exists();

            if ($exists) {
                continue;
            }

            AppNotification::create([
                'user_id' => $owner->id,
                'type' => 'low_stock',
                'title' => $title,
                'body' => $body,
                'link' => $link,
            ]);
        }
    }
}
