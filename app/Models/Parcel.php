<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    protected $fillable = [
        'tracking_id',
        'from_town',
        'to_town',
        'city_code',
        'account_code',
        'sender_name',
        'sender_phone',
        'receiver_name',
        'receiver_phone',
        'parcel_type',
        'number_of_parcels',
        'total_charges',
        'payment_status',
        'cash_advance',
        'parcel_image_path',
        'remark',
        'status',
        'sync_status',
        'synced_at',
        'arrived_at',
        'claimed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_charges' => 'decimal:2',
            'cash_advance' => 'decimal:2',
            'synced_at' => 'datetime',
            'arrived_at' => 'datetime',
            'claimed_at' => 'datetime',
        ];
    }

    public function fromTown(): BelongsTo
    {
        return $this->belongsTo(Town::class, 'from_town');
    }

    public function toTown(): BelongsTo
    {
        return $this->belongsTo(Town::class, 'to_town');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ParcelStatusLog::class);
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(SyncLog::class);
    }
}
