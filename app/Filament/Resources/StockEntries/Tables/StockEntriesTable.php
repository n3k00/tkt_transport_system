<?php

namespace App\Filament\Resources\StockEntries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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
                TextColumn::make('received_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
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
                Filter::make('received_date')
                    ->form([
                        DatePicker::make('received_from'),
                        DatePicker::make('received_until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['received_from'] ?? null, fn ($query, $date) => $query->whereDate('received_date', '>=', $date))
                            ->when($data['received_until'] ?? null, fn ($query, $date) => $query->whereDate('received_date', '<=', $date));
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
