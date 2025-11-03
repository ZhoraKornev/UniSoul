<?php

namespace App\Filament\Resources\Confessions\Pages;

use App\Filament\Resources\Confessions\ConfessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConfessions extends ListRecords
{
    protected static string $resource = ConfessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
