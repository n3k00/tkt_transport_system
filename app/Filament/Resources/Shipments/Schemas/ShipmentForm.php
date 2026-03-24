<?php

namespace App\Filament\Resources\Shipments\Schemas;

use App\Models\Driver;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ShipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('shipment_no')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                DatePicker::make('shipment_date')
                    ->required()
                    ->default(now()),
                Select::make('driver_id')
                    ->relationship('driver', 'name', fn ($query) => $query->where('is_active', true))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, $set): void {
                        $set('car_number', Driver::query()->find($state)?->default_car_number);
                    }),
                TextInput::make('car_number')
                    ->required()
                    ->maxLength(50),
                TextInput::make('total_amount')
                    ->disabled()
                    ->dehydrated()
                    ->numeric()
                    ->default(0)
                    ->helperText('Calculated from shipment items.'),
                Textarea::make('remark')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
