<?php

namespace App\Filament\Resources\Towns\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TownInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('town_name'),
                TextEntry::make('type'),
                TextEntry::make('city_code')
                    ->placeholder('-'),
                TextEntry::make('sort_order')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
