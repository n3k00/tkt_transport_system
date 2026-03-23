<?php

namespace App\Filament\Resources\Parcels\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ParcelInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tracking_id'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('from_town')
                    ->numeric(),
                TextEntry::make('to_town')
                    ->numeric(),
                TextEntry::make('city_code'),
                TextEntry::make('account_code'),
                TextEntry::make('sender_name'),
                TextEntry::make('sender_phone'),
                TextEntry::make('receiver_name'),
                TextEntry::make('receiver_phone'),
                TextEntry::make('parcel_type'),
                TextEntry::make('number_of_parcels')
                    ->numeric(),
                TextEntry::make('total_charges')
                    ->numeric(),
                TextEntry::make('payment_status'),
                TextEntry::make('cash_advance')
                    ->numeric(),
                ImageEntry::make('parcel_image_path')
                    ->placeholder('-'),
                TextEntry::make('remark')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('status'),
                TextEntry::make('sync_status'),
                TextEntry::make('synced_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('arrived_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('claimed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
