<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'shipment_no',
        'shipment_date',
        'driver_id',
        'car_number',
        'total_amount',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'shipment_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function shipmentItems(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function recalculateTotalAmount(): void
    {
        $this->updateQuietly([
            'total_amount' => $this->shipmentItems()->sum('line_total'),
        ]);
    }
}
