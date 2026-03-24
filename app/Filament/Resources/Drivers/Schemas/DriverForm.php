<?php

namespace App\Filament\Resources\Drivers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DriverForm
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
                TextInput::make('default_car_number')
                    ->maxLength(50),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
                Textarea::make('remark')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
