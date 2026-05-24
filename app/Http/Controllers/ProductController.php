<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $q = trim((string) request()->query('q', ''));

        $products = Product::query()
            ->with('supplier')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function ($q2) use ($like) {
                    $q2->where('products.name', 'like', $like)
                        ->orWhere('products.category', 'like', $like)
                        ->orWhereHas('supplier', fn ($qs) => $qs->where('name', 'like', $like));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::query()->orderBy('name')->get();

        return view('products.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['nullable', 'string', 'max:50'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $data['stock'] = (int) ($data['stock'] ?? 0);

        $product = Product::create($data);
        $this->createLowStockNotificationsIfNeeded($product);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return redirect()->route('products.edit', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $suppliers = Supplier::query()->orderBy('name')->get();

        return view('products.edit', compact('product', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['nullable', 'string', 'max:50'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $product->update($data);
        $this->createLowStockNotificationsIfNeeded($product->fresh());

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function createLowStockNotificationsIfNeeded(Product $product): void
    {
        if ($product->stock > $product->min_stock) {
            return;
        }

        $link = route('products.edit', $product);
        $title = 'Stok menipis: '.$product->name;
        $body = 'Stok: '.$product->stock.' | Minimum: '.$product->min_stock;

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
