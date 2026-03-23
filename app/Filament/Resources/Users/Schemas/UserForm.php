<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(11)
                    ->rule('regex:/^09\d{9}$/')
                    ->helperText('Myanmar local format only: 09xxxxxxxxx'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state): bool => filled($state)),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'staff' => 'Staff',
                    ])
                    ->required(),
                TextInput::make('account_code')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
