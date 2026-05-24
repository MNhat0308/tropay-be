<?php

namespace App\Filament\Resources\BillRoomResource\Pages;

use App\Filament\Resources\BillRoomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBillRoom extends CreateRecord
{
    protected static string $resource = BillRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
