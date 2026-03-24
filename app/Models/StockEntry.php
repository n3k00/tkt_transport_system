<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    protected $fillable = [
        'merchant_id',
        'item_id',
        'quantity',
        'unit_price',
        'line_total',
        'received_date',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'received_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (StockEntry $stockEntry): void {
            $stockEntry->line_total = round((float) $stockEntry->quantity * (float) $stockEntry->unit_price, 2);
        });
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
