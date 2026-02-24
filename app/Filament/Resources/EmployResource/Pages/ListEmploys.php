<?php

namespace App\Filament\Resources\EmployResource\Pages;

use App\Filament\Resources\EmployResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmploys extends ListRecords
{
    protected static string $resource = EmployResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
