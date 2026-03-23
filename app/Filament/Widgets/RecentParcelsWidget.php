<?php

namespace App\Filament\Widgets;

use App\Models\Parcel;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentParcelsWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Parcels';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Parcel::query()
                    ->with(['fromTown:id,town_name', 'toTown:id,town_name'])
                    ->latest()
            )
            ->columns([
                TextColumn::make('tracking_id')
                    ->searchable(),
                TextColumn::make('fromTown.town_name')
                    ->label('From'),
                TextColumn::make('toTown.town_name')
                    ->label('To'),
                TextColumn::make('sender_name')
                    ->label('Sender')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'received' => 'gray',
                        'dispatched' => 'info',
                        'arrived' => 'warning',
                        'claimed' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('sync_status')
                    ->label('Sync')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'synced' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Received At')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
