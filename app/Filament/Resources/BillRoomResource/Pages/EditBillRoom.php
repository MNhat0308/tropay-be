<?php

namespace App\Filament\Resources\BillRoomResource\Pages;

use App\Filament\Resources\BillRoomResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditBillRoom extends EditRecord
{
    protected static string $resource = BillRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
