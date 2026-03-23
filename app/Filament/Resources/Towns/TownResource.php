<?php

namespace App\Filament\Resources\Towns;

use App\Filament\Resources\Towns\Pages\CreateTown;
use App\Filament\Resources\Towns\Pages\EditTown;
use App\Filament\Resources\Towns\Pages\ListTowns;
use App\Filament\Resources\Towns\Pages\ViewTown;
use App\Filament\Resources\Towns\Schemas\TownForm;
use App\Filament\Resources\Towns\Schemas\TownInfolist;
use App\Filament\Resources\Towns\Tables\TownsTable;
use App\Models\Town;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TownResource extends Resource
{
    protected static ?string $model = Town::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'town_name';

    public static function form(Schema $schema): Schema
    {
        return TownForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TownInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TownsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTowns::route('/'),
            'create' => CreateTown::route('/create'),
            'view' => ViewTown::route('/{record}'),
            'edit' => EditTown::route('/{record}/edit'),
        ];
    }
}
