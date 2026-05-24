<?php

namespace App\Filament\Resources\ResidencesResource\Pages;

use App\Filament\Resources\ResidencesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResidences extends ListRecords
{
    protected static string $resource = ResidencesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
