<?php

namespace App\Filament\Resources\Towns\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TownForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('town_name')
                    ->required(),
                Select::make('type')
                    ->options([
                        'source' => 'Source',
                        'destination' => 'Destination',
                    ])
                    ->required()
                    ->live(),
                TextInput::make('city_code')
                    ->maxLength(20)
                    ->required(fn ($get) => $get('type') === 'source')
                    ->visible(fn ($get) => $get('type') === 'source'),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
