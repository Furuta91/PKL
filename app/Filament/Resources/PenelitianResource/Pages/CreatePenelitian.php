<?php

namespace App\Filament\Resources\PenelitianResource\Pages;

use App\Filament\Resources\PenelitianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePenelitian extends CreateRecord
{
    protected static string $resource = PenelitianResource::class;

    protected function getRedirectUrl(): string
{
    return $this->getResource()::getUrl('create');
}
}