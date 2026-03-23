<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    protected $fillable = [
        'town_name',
        'type',
        'city_code',
        'sort_order',
    ];

    public function parcelsFrom(): HasMany
    {
        return $this->hasMany(Parcel::class, 'from_town');
    }

    public function parcelsTo(): HasMany
    {
        return $this->hasMany(Parcel::class, 'to_town');
    }
}
