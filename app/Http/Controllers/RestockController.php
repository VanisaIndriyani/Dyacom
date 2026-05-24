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
        $products = Product::query()->orderBy('name')->get();

        return view('restock.create', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string'],
        ]);

        $restock = RestockRequest::create([
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'note' => $data['note'] ?? null,
            'status' => 'pending',
            'requested_by' => $request->user()->id,
        ]);

        $product = $restock->product()->first();
        $link = route('restock.index');

        $owners = User::query()->where('role', 'owner')->get();

        foreach ($owners as $owner) {
            AppNotification::create([
                'user_id' => $owner->id,
                'type' => 'restock_request',
                'title' => 'Pengajuan restok baru',
                'body' => ($product ? $product->name : 'Produk').' | Qty: '.$restock->quantity,
                'link' => $link,
            ]);
        }

        return redirect()->route('restock.index')->with('success', 'Pengajuan restok berhasil dikirim.');
    }

    public function approve(Request $request, RestockRequest $restock)
    {
        if ($request->user()->role !== 'owner') {
            abort(403);
        }

        if ($restock->status !== 'pending') {
            return redirect()->route('restock.index')->with('success', 'Pengajuan sudah diproses.');
        }

        $data = $request->validate([
            'decision_note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request, $restock, $data) {
            $restock->update([
                'status' => 'approved',
                'decided_by' => $request->user()->id,
                'decided_at' => now(),
                'decision_note' => $data['decision_note'] ?? null,
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
                'note' => 'Restok disetujui'.(($data['decision_note'] ?? null) ? ' - '.$data['decision_note'] : ''),
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

        $data = $request->validate([
            'decision_note' => ['nullable', 'string'],
        ]);

        $restock->update([
            'status' => 'rejected',
            'decided_by' => $request->user()->id,
            'decided_at' => now(),
            'decision_note' => $data['decision_note'] ?? null,
        ]);

        $product = $restock->product()->first();

        AppNotification::create([
            'user_id' => $restock->requested_by,
            'type' => 'restock_status',
            'title' => 'Pengajuan restok ditolak',
            'body' => ($product ? $product->name : 'Produk').' | Qty: '.$restock->quantity.((($data['decision_note'] ?? null)) ? ' | Catatan: '.$data['decision_note'] : ''),
            'link' => route('restock.index'),
        ]);

        return redirect()->route('restock.index')->with('success', 'Pengajuan restok ditolak.');
    }
}
