<?php

namespace App\Filament\Resources\Towns\Pages;

use App\Filament\Resources\Towns\TownResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTown extends ViewRecord
{
    protected static string $resource = TownResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
