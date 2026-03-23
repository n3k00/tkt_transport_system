<?php

namespace App\Filament\Resources\Parcels\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('cash_advance')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('parcel_image_path'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'received' => 'gray',
                        'dispatched' => 'info',
                        'arrived' => 'warning',
                        'claimed' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('sync_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'synced' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
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
                SelectFilter::make('status')
                    ->options([
                        'received' => 'Received',
                        'dispatched' => 'Dispatched',
                        'arrived' => 'Arrived',
                        'claimed' => 'Claimed',
                    ]),
                SelectFilter::make('sync_status')
                    ->options([
                        'pending' => 'Pending',
                        'synced' => 'Synced',
                        'failed' => 'Failed',
                    ]),
                SelectFilter::make('payment_status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                    ]),
                SelectFilter::make('from_town')
                    ->label('From town')
                    ->relationship('fromTown', 'town_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('to_town')
                    ->label('To town')
                    ->relationship('toTown', 'town_name')
                    ->searchable()
                    ->preload(),
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
