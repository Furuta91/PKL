<?php

namespace App\Filament\Resources\PengabdianResource\Pages;

use App\Filament\Resources\PengabdianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengabdians extends ListRecords
{
    protected static string $resource = PengabdianResource::class;

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
        session(['last_pengabdian_url' => url()->full()]);
    }
}
