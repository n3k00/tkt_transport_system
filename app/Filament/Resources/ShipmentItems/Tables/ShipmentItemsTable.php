<?php

namespace App\Filament\Resources\ShipmentItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ShipmentItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shipment.shipment_no')
                    ->label('Shipment no')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('merchant.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('item.item_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('line_total')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('shipment_id')
                    ->label('Shipment')
                    ->relationship('shipment', 'shipment_no')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('merchant_id')
                    ->label('Merchant')
                    ->relationship('merchant', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('item_id')
                    ->label('Item')
                    ->relationship('item', 'item_name')
                    ->searchable()
                    ->preload(),
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
