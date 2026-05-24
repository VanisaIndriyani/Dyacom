<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RestockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'note',
        'status',
        'requested_by',
        'decided_by',
        'decided_at',
        'decision_note',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function stockMovement(): HasOne
    {
        return $this->hasOne(StockMovement::class);
    }
}
