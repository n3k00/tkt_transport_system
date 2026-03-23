<?php

namespace App\Filament\Resources\Towns\Pages;

use App\Filament\Resources\Towns\TownResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTown extends EditRecord
{
    protected static string $resource = TownResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
