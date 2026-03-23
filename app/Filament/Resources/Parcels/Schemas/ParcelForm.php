<?php

namespace App\Filament\Resources\Parcels\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ParcelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tracking_id')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('from_town')
                    ->label('From town')
                    ->relationship('fromTown', 'town_name', fn ($query) => $query->where('type', 'source'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('to_town')
                    ->label('To town')
                    ->relationship('toTown', 'town_name', fn ($query) => $query->where('type', 'destination'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('city_code')
                    ->required(),
                TextInput::make('account_code')
                    ->required(),
                TextInput::make('sender_name')
                    ->required(),
                TextInput::make('sender_phone')
                    ->tel()
                    ->required(),
                TextInput::make('receiver_name')
                    ->required(),
                TextInput::make('receiver_phone')
                    ->tel()
                    ->required(),
                TextInput::make('parcel_type')
                    ->required(),
                TextInput::make('number_of_parcels')
                    ->required()
                    ->numeric(),
                TextInput::make('total_charges')
                    ->required()
                    ->numeric(),
                Select::make('payment_status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                    ])
                    ->required(),
                TextInput::make('cash_advance')
                    ->required()
                    ->numeric()
                    ->default(0),
                FileUpload::make('parcel_image_path')
                    ->image(),
                Textarea::make('remark')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'received' => 'Received',
                        'dispatched' => 'Dispatched',
                        'arrived' => 'Arrived',
                        'claimed' => 'Claimed',
                    ])
                    ->required()
                    ->default('received'),
                Select::make('sync_status')
                    ->options([
                        'pending' => 'Pending',
                        'synced' => 'Synced',
                        'failed' => 'Failed',
                    ])
                    ->required()
                    ->default('pending'),
                DateTimePicker::make('synced_at'),
                DateTimePicker::make('arrived_at'),
                DateTimePicker::make('claimed_at'),
            ]);
    }
}
