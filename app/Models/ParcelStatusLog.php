<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParcelStatusLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'parcel_id',
        'previous_status',
        'new_status',
        'changed_by',
        'note',
        'created_at',
    ];

    public function parcel(): BelongsTo
    {
        return $this->belongsTo(Parcel::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
