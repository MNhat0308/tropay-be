<?php

namespace App\Filament\Imports;

use App\Models\BillRoom;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class BillRoomImporter extends Importer
{
    protected static ?string $model = BillRoom::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('at')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('old_electric')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('new_electric')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('old_water')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('new_water')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('price_water')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('price_electric')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('price_room')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('price_garbage')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('room_id')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('electric_consumption')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('water_consumption')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('total_price')
                ->numeric()
                ->rules(['integer']),
        ];
    }

    public function resolveRecord(): ?BillRoom
    {
        // return BillRoom::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new BillRoom();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your bill room import has completed and '.number_format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if (($failedRowsCount = $import->getFailedRowsCount()) !== 0) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
