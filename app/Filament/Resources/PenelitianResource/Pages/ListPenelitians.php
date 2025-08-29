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

 public function mount(): void
    {
        parent::mount();

        // simpan query string terakhir user (page, search, filter, sort, dll.)
        session(['last_penelitian_url' => url()->full()]);
    }


}