<?php

namespace App\Models;

use App\Services\MerchantStockService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class ShipmentItem extends Model
{
    protected $fillable = [
        'shipment_id',
        'merchant_id',
        'item_id',
        'quantity',
        'unit_price',
        'line_total',
        'remark',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (ShipmentItem $shipmentItem): void {
            $shipmentItem->line_total = round((float) $shipmentItem->quantity * (float) $shipmentItem->unit_price, 2);

            if (! $shipmentItem->merchant_id || ! $shipmentItem->item_id) {
                return;
            }

            $availableBalance = app(MerchantStockService::class)->getAvailableBalanceForMerchantItem(
                merchantId: $shipmentItem->merchant_id,
                itemId: $shipmentItem->item_id,
                excludeShipmentItemId: $shipmentItem->exists ? $shipmentItem->id : null,
            );

            if ((float) $shipmentItem->quantity > $availableBalance) {
                throw ValidationException::withMessages([
                    'quantity' => sprintf(
                        'Insufficient stock. Available balance for this merchant and item is %.2f.',
                        $availableBalance,
                    ),
                ]);
            }
        });

        static::saved(function (ShipmentItem $shipmentItem): void {
            $shipmentItem->shipment?->recalculateTotalAmount();
        });

        static::deleted(function (ShipmentItem $shipmentItem): void {
            $shipmentItem->shipment?->recalculateTotalAmount();
        });
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
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
