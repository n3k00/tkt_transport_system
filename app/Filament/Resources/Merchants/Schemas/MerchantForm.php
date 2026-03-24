<?php

namespace App\Filament\Resources\Merchants\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MerchantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(20),
                Textarea::make('address')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('telegram_username')
                    ->prefix('@')
                    ->maxLength(100),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
                Textarea::make('remark')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
