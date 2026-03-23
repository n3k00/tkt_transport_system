<?php

namespace App\Filament\Resources\Parcels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParcelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_id')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fromTown.town_name')
                    ->label('From town')
                    ->sortable(),
                TextColumn::make('toTown.town_name')
                    ->label('To town')
                    ->sortable(),
                TextColumn::make('city_code')
                    ->searchable(),
                TextColumn::make('account_code')
                    ->searchable(),
                TextColumn::make('sender_name')
                    ->searchable(),
                TextColumn::make('sender_phone')
                    ->searchable(),
                TextColumn::make('receiver_name')
                    ->searchable(),
                TextColumn::make('receiver_phone')
                    ->searchable(),
                TextColumn::make('parcel_type')
                    ->searchable(),
                TextColumn::make('number_of_parcels')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_charges')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->searchable(),
                TextColumn::make('cash_advance')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('parcel_image_path'),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('sync_status')
                    ->searchable(),
                TextColumn::make('synced_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('arrived_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('claimed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
