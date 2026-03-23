<?php

namespace App\Filament\Widgets;

use App\Models\Parcel;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ParcelStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Parcel Overview';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        return [
            Stat::make('Total Parcels', number_format(Parcel::query()->count()))
                ->description('All parcel records in the system'),
            Stat::make('Received Today', number_format(Parcel::query()->whereDate('created_at', today())->count()))
                ->description('New parcels received today'),
            Stat::make('Arrived Today', number_format(Parcel::query()->whereDate('arrived_at', today())->count()))
                ->description('Parcels marked as arrived today'),
            Stat::make('Claimed Today', number_format(Parcel::query()->whereDate('claimed_at', today())->count()))
                ->description('Parcels claimed today'),
        ];
    }
}
