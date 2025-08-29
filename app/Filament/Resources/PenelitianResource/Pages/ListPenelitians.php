<?php

namespace App\Filament\Resources\PenelitianResource\Pages;

use App\Filament\Resources\PenelitianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenelitians extends ListRecords
{
    protected static string $resource = PenelitianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
