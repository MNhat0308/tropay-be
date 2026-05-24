<?php

namespace App\Filament\Resources\ResidencesResource\Pages;

use App\Filament\Resources\ResidencesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateResidences extends CreateRecord
{
    protected static string $resource = ResidencesResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
