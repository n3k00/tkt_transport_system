<?php

namespace App\Filament\Resources\ShipmentItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ShipmentItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('shipment_id')
                    ->relationship('shipment', 'shipment_no')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('merchant_id')
                    ->relationship('merchant', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('item_id')
                    ->relationship('item', 'item_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $get, $set) => $set('line_total', round((float) $state * (float) ($get('unit_price') ?? 0), 2))),
                TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $get, $set) => $set('line_total', round((float) ($get('quantity') ?? 0) * (float) $state, 2))),
                TextInput::make('line_total')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->default(0),
                Textarea::make('remark')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
