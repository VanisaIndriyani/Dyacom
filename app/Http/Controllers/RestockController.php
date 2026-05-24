<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Product;
use App\Models\RestockRequest;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RestockController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role === 'owner') {
            $batches = RestockRequest::query()
                ->join('users', 'users.id', '=', 'restock_requests.requested_by')
                ->select([
                    'restock_requests.requested_by',
                    DB::raw("DATE_FORMAT(restock_requests.created_at, '%Y-%m-%d %H:%i:%s') as batch_time"),
                    DB::raw('MIN(restock_requests.id) as first_id'),
                    DB::raw('COUNT(*) as total_items'),
                    DB::raw("SUM(CASE WHEN restock_requests.status = 'pending' THEN 1 ELSE 0 END) as pending_items"),
                    DB::raw("SUM(CASE WHEN restock_requests.status = 'approved' THEN 1 ELSE 0 END) as approved_items"),
                    DB::raw("SUM(CASE WHEN restock_requests.status = 'rejected' THEN 1 ELSE 0 END) as rejected_items"),
                    'users.name as requester_name',
                ])
                ->groupBy('restock_requests.requested_by', 'batch_time', 'users.name')
                ->orderByDesc('batch_time')
                ->paginate(10)
                ->withQueryString();

            return view('restock.index', compact('batches'));
        }

        $q = trim((string) $request->query('q', ''));

        $query = RestockRequest::query()
            ->with(['product', 'requester', 'decider'])
            ->latest()
            ->where('requested_by', $request->user()->id);

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
        if (request()->user()->role === 'owner') {
            return redirect()->route('restock.index')->with('error', 'Hanya karyawan yang dapat mengajukan restok.');
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
        if ($request->user()->role === 'owner') {
            return redirect()->route('restock.index')->with('error', 'Hanya karyawan yang dapat mengajukan restok.');
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

        $batchRequests = collect();
        $batchTime = null;
        $mode = (string) $request->query('mode', '');

        if ($request->user()->role === 'owner') {
            $batchStart = $restock->created_at->copy()->setMicrosecond(0);
            $batchEnd = $batchStart->copy()->addSecond();
            $batchTime = $batchStart->format('Y-m-d H:i:s');

            $batchRequests = RestockRequest::query()
                ->with(['product'])
                ->where('requested_by', $restock->requested_by)
                ->where('created_at', '>=', $batchStart)
                ->where('created_at', '<', $batchEnd)
                ->orderBy('id')
                ->get();
        }

        return view('restock.show', compact('restock', 'batchRequests', 'batchTime', 'mode'));
    }

    public function bulkDecide(Request $request)
    {
        if ($request->user()->role !== 'owner') {
            return redirect()->route('restock.index')->with('error', 'Hanya pemilik toko yang dapat memproses pengajuan.');
        }

        $data = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'batch_requested_by' => ['required', 'integer', 'exists:users,id'],
            'batch_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'restock_ids' => ['required', 'array', 'min:1'],
            'restock_ids.*' => ['integer'],
            'decision_note' => ['nullable', 'string', 'max:500'],
        ]);

        $batchStart = Carbon::createFromFormat('Y-m-d H:i:s', $data['batch_time']);
        $batchEnd = (clone $batchStart)->addSecond();

        $targets = RestockRequest::query()
            ->with(['product'])
            ->where('requested_by', (int) $data['batch_requested_by'])
            ->where('created_at', '>=', $batchStart)
            ->where('created_at', '<', $batchEnd)
            ->whereIn('id', array_map('intval', $data['restock_ids']))
            ->get();

        if ($targets->isEmpty()) {
            return redirect()->route('restock.index')->with('error', 'Tidak ada item yang bisa diproses.');
        }

        $decisionNote = $data['decision_note'] !== null ? trim($data['decision_note']) : null;
        $now = now();
        $processed = 0;

        DB::transaction(function () use ($request, $targets, $data, $decisionNote, $now, &$processed) {
            foreach ($targets as $restock) {
                if ($restock->status !== 'pending') {
                    continue;
                }

                if ($data['action'] === 'approve') {
                    $restock->update([
                        'status' => 'approved',
                        'decided_by' => $request->user()->id,
                        'decided_at' => $now,
                        'decision_note' => $decisionNote,
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

                    $body = $product->name.' | Qty: '.$restock->quantity;
                    if ($decisionNote) {
                        $body .= ' | Ket: '.$decisionNote;
                    }

                    AppNotification::create([
                        'user_id' => $restock->requested_by,
                        'type' => 'restock_status',
                        'title' => 'Pengajuan restok disetujui',
                        'body' => $body,
                        'link' => route('restock.index'),
                    ]);
                } else {
                    $restock->update([
                        'status' => 'rejected',
                        'decided_by' => $request->user()->id,
                        'decided_at' => $now,
                        'decision_note' => $decisionNote,
                    ]);

                    $product = $restock->product;

                    $body = ($product ? $product->name : 'Produk').' | Qty: '.$restock->quantity;
                    if ($decisionNote) {
                        $body .= ' | Ket: '.$decisionNote;
                    }

                    AppNotification::create([
                        'user_id' => $restock->requested_by,
                        'type' => 'restock_status',
                        'title' => 'Pengajuan restok ditolak',
                        'body' => $body,
                        'link' => route('restock.index'),
                    ]);
                }

                $processed++;
            }
        });

        if ($processed < 1) {
            return back()->with('success', 'Semua item yang dipilih sudah diproses.');
        }

        return back()->with('success', $data['action'] === 'approve' ? 'Item terpilih berhasil disetujui.' : 'Item terpilih berhasil ditolak.');
    }

    public function bulkUpdateDecisionNote(Request $request)
    {
        if ($request->user()->role !== 'owner') {
            return redirect()->route('restock.index')->with('error', 'Hanya pemilik toko yang dapat mengubah keterangan.');
        }

        $data = $request->validate([
            'batch_requested_by' => ['required', 'integer', 'exists:users,id'],
            'batch_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'decision_note' => ['nullable', 'string', 'max:500'],
        ]);

        $batchStart = Carbon::createFromFormat('Y-m-d H:i:s', $data['batch_time']);
        $batchEnd = (clone $batchStart)->addSecond();

        $note = $data['decision_note'] !== null ? trim($data['decision_note']) : null;

        $items = RestockRequest::query()
            ->with(['product'])
            ->where('requested_by', (int) $data['batch_requested_by'])
            ->where('created_at', '>=', $batchStart)
            ->where('created_at', '<', $batchEnd)
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('restock.index')->with('error', 'Pengajuan tidak ditemukan.');
        }

        RestockRequest::query()
            ->whereIn('id', $items->pluck('id')->all())
            ->update(['decision_note' => $note]);

        $names = $items->pluck('product.name')->filter()->values()->all();
        $body = count($names) > 1
            ? implode(', ', array_slice($names, 0, 3)).(count($names) > 3 ? '...' : '').' | Total item: '.count($names)
            : (($names[0] ?? 'Produk').' | Total item: 1');
        $body .= ' | Ket: '.($note !== null && $note !== '' ? $note : '-');

        AppNotification::create([
            'user_id' => (int) $data['batch_requested_by'],
            'type' => 'restock_status',
            'title' => 'Keterangan pengajuan restok diperbarui',
            'body' => $body,
            'link' => route('restock.index'),
        ]);

        return back()->with('success', 'Keterangan berhasil diperbarui dan dikirim ke karyawan.');
    }

    public function approve(Request $request, RestockRequest $restock)
    {
        if ($request->user()->role !== 'owner') {
            return redirect()->route('restock.index')->with('error', 'Hanya pemilik toko yang dapat memproses pengajuan.');
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
            return redirect()->route('restock.index')->with('error', 'Hanya pemilik toko yang dapat memproses pengajuan.');
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
