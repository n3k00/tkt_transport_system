<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Item;
use App\Models\ShipmentItem;
use App\Models\StockEntry;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MerchantStockService
{
    public function getBalanceForMerchantItem(int $merchantId, int $itemId): float
    {
        $incomingQuantity = (float) StockEntry::query()
            ->where('merchant_id', $merchantId)
            ->where('item_id', $itemId)
            ->sum('quantity');

        $dispatchedQuantity = (float) ShipmentItem::query()
            ->where('merchant_id', $merchantId)
            ->where('item_id', $itemId)
            ->sum('quantity');

        return round($incomingQuantity - $dispatchedQuantity, 2);
    }

    public function getAvailableBalanceForMerchantItem(
        int $merchantId,
        int $itemId,
        ?int $excludeShipmentItemId = null,
    ): float {
        $incomingQuantity = (float) StockEntry::query()
            ->where('merchant_id', $merchantId)
            ->where('item_id', $itemId)
            ->sum('quantity');

        $dispatchedQuery = ShipmentItem::query()
            ->where('merchant_id', $merchantId)
            ->where('item_id', $itemId);

        if ($excludeShipmentItemId) {
            $dispatchedQuery->whereKeyNot($excludeShipmentItemId);
        }

        $dispatchedQuantity = (float) $dispatchedQuery->sum('quantity');

        return round($incomingQuantity - $dispatchedQuantity, 2);
    }

    public function getBalancesForMerchant(int $merchantId): Collection
    {
        $incoming = StockEntry::query()
            ->select('item_id', DB::raw('SUM(quantity) as incoming_quantity'))
            ->where('merchant_id', $merchantId)
            ->groupBy('item_id');

        return ShipmentItem::query()
            ->selectRaw('stock_entries.item_id')
            ->selectRaw('stock_entries.incoming_quantity')
            ->selectRaw('COALESCE(SUM(shipment_items.quantity), 0) as dispatched_quantity')
            ->selectRaw('stock_entries.incoming_quantity - COALESCE(SUM(shipment_items.quantity), 0) as remaining_quantity')
            ->rightJoinSub($incoming, 'stock_entries', function ($join) use ($merchantId): void {
                $join->on('shipment_items.item_id', '=', 'stock_entries.item_id')
                    ->where('shipment_items.merchant_id', '=', $merchantId);
            })
            ->groupBy('stock_entries.item_id', 'stock_entries.incoming_quantity')
            ->get();
    }

    public function getBalanceRows(): EloquentCollection
    {
        $incoming = StockEntry::query()
            ->select('merchant_id', 'item_id')
            ->selectRaw('SUM(quantity) as incoming_quantity')
            ->groupBy('merchant_id', 'item_id');

        $dispatched = ShipmentItem::query()
            ->select('merchant_id', 'item_id')
            ->selectRaw('SUM(quantity) as dispatched_quantity')
            ->groupBy('merchant_id', 'item_id');

        return Merchant::query()
            ->select('merchants.name as merchant_name')
            ->select('items.item_name')
            ->select('items.unit')
            ->selectRaw('COALESCE(incoming.incoming_quantity, 0) as incoming_quantity')
            ->selectRaw('COALESCE(dispatched.dispatched_quantity, 0) as dispatched_quantity')
            ->selectRaw('COALESCE(incoming.incoming_quantity, 0) - COALESCE(dispatched.dispatched_quantity, 0) as remaining_quantity')
            ->joinSub($incoming, 'incoming', fn ($join) => $join->on('incoming.merchant_id', '=', 'merchants.id'))
            ->join('items', 'items.id', '=', 'incoming.item_id')
            ->leftJoinSub($dispatched, 'dispatched', function ($join): void {
                $join->on('dispatched.merchant_id', '=', 'incoming.merchant_id')
                    ->on('dispatched.item_id', '=', 'incoming.item_id');
            })
            ->orderBy('merchants.name')
            ->orderBy('items.item_name')
            ->get();
    }
}
