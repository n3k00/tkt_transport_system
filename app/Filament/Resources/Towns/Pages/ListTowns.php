<?php

namespace App\Filament\Resources\Towns\Pages;

use App\Filament\Resources\Towns\TownResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTowns extends ListRecords
{
    protected static string $resource = TownResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
