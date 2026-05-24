<?php

namespace App\Filament\Resources\ResidencesResource\Pages;

use App\Filament\Resources\ResidencesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditResidences extends EditRecord
{
    protected static string $resource = ResidencesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
