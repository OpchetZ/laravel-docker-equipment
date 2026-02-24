<?php

namespace App\Filament\Resources\EmployResource\Pages;

use App\Filament\Resources\EmployResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmploy extends EditRecord
{
    protected static string $resource = EmployResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
