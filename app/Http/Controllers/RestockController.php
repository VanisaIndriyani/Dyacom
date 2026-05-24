<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Product;
use App\Models\RestockRequest;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestockController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = RestockRequest::query()
            ->with(['product', 'requester', 'decider'])
            ->latest();

        if ($request->user()->role === 'employee') {
            $query->where('requested_by', $request->user()->id);
        }

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($q2) use ($like) {
                $q2->where('status', 'like', $like)
                    ->orWhereHas('product', fn ($qp) => $qp->where('name', 'like', $like))
                    ->orWhereHas('requester', fn ($qu) => $qu->where('name', 'like', $like)->orWhere('email', 'like', $like));
            });
        }

        $requests = $query->paginate(10)->withQueryString();

        return view('restock.index', compact('requests'));
    }

    public function create()
    {
        if (request()->user()->role !== 'employee') {
            abort(403);
        }

        $products = Product::query()
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->orderBy('name')
            ->get(['id', 'name', 'unit', 'stock', 'min_stock']);

        return view('restock.create', compact('products'));
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'employee') {
            abort(403);
        }

        $data = $request->validate([
            'products' => ['required', 'array', 'min:1'],
            'products.*' => ['integer', 'exists:products,id'],
            'quantities' => ['required', 'array'],
        ]);

        $selectedProducts = array_values(array_unique(array_map('intval', $data['products'])));
        $quantities = $data['quantities'] ?? [];

        $errors = [];
        foreach ($selectedProducts as $productId) {
            $qty = (int) ($quantities[$productId] ?? 0);
            if ($qty < 1) {
                $errors['quantities.'.$productId] = 'Jumlah restok wajib diisi.';
            }
        }
        if ($errors) {
            return back()->withErrors($errors)->withInput();
        }

        $created = [];
        DB::transaction(function () use ($request, $selectedProducts, $quantities, &$created) {
            foreach ($selectedProducts as $productId) {
                $created[] = RestockRequest::create([
                    'product_id' => $productId,
                    'quantity' => (int) $quantities[$productId],
                    'note' => null,
                    'status' => 'pending',
                    'requested_by' => $request->user()->id,
                ]);
            }
        });

        $products = Product::query()->whereIn('id', $selectedProducts)->get(['id', 'name']);
        $names = $products->pluck('name')->values()->all();

        $title = 'Pengajuan restok baru';
        $body = count($names) > 1
            ? implode(', ', array_slice($names, 0, 3)).(count($names) > 3 ? '...' : '').' | Total item: '.count($names)
            : (($names[0] ?? 'Produk').' | Total item: 1');

        $link = route('restock.index');
        $owners = User::query()->where('role', 'owner')->get();

        foreach ($owners as $owner) {
            AppNotification::create([
                'user_id' => $owner->id,
                'type' => 'restock_request',
                'title' => $title,
                'body' => $body,
                'link' => $link,
            ]);
        }

        return redirect()->route('restock.index')->with('success', 'Pengajuan restok berhasil dikirim.');
    }

    public function show(Request $request, RestockRequest $restock)
    {
        $restock->load(['product', 'requester', 'decider']);

        if ($request->user()->role === 'employee' && $restock->requested_by !== $request->user()->id) {
            abort(403);
        }

        return view('restock.show', compact('restock'));
    }

    public function approve(Request $request, RestockRequest $restock)
    {
        if ($request->user()->role !== 'owner') {
            abort(403);
        }

        if ($restock->status !== 'pending') {
            return redirect()->route('restock.index')->with('success', 'Pengajuan sudah diproses.');
        }

        DB::transaction(function () use ($request, $restock) {
            $restock->update([
                'status' => 'approved',
                'decided_by' => $request->user()->id,
                'decided_at' => now(),
                'decision_note' => null,
            ]);

            $product = Product::query()->lockForUpdate()->findOrFail($restock->product_id);
            $product->update(['stock' => $product->stock + $restock->quantity]);

            StockMovement::create([
                'product_id' => $product->id,
                'supplier_id' => null,
                'user_id' => $request->user()->id,
                'restock_request_id' => $restock->id,
                'type' => 'in',
                'quantity' => $restock->quantity,
                'note' => 'Restok disetujui',
            ]);

            AppNotification::create([
                'user_id' => $restock->requested_by,
                'type' => 'restock_status',
                'title' => 'Pengajuan restok disetujui',
                'body' => $product->name.' | Qty: '.$restock->quantity,
                'link' => route('restock.index'),
            ]);
        });

        return redirect()->route('restock.index')->with('success', 'Pengajuan restok disetujui.');
    }

    public function reject(Request $request, RestockRequest $restock)
    {
        if ($request->user()->role !== 'owner') {
            abort(403);
        }

        if ($restock->status !== 'pending') {
            return redirect()->route('restock.index')->with('success', 'Pengajuan sudah diproses.');
        }

        $restock->update([
            'status' => 'rejected',
            'decided_by' => $request->user()->id,
            'decided_at' => now(),
            'decision_note' => null,
        ]);

        $product = $restock->product()->first();

        AppNotification::create([
            'user_id' => $restock->requested_by,
            'type' => 'restock_status',
            'title' => 'Pengajuan restok ditolak',
            'body' => ($product ? $product->name : 'Produk').' | Qty: '.$restock->quantity,
            'link' => route('restock.index'),
        ]);

        return redirect()->route('restock.index')->with('success', 'Pengajuan restok ditolak.');
    }
}
