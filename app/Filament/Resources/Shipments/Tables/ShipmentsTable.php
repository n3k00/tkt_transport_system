<?php

namespace App\Filament\Resources\Shipments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shipment_no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('shipment_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('driver.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('car_number')
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('driver_id')
                    ->label('Driver')
                    ->relationship('driver', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('shipment_date')
                    ->form([
                        DatePicker::make('shipment_from'),
                        DatePicker::make('shipment_until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['shipment_from'] ?? null, fn ($query, $date) => $query->whereDate('shipment_date', '>=', $date))
                            ->when($data['shipment_until'] ?? null, fn ($query, $date) => $query->whereDate('shipment_date', '<=', $date));
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
