<?php

namespace App\Filament\Resources\BillRoomResource\Pages;

use App\Filament\Resources\BillRoomResource;
use App\Models\BillRoom;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBillRooms extends ListRecords
{
    protected static string $resource = BillRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                ->badge(BillRoom::query()->count()),
            'Last Month' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->lastMonthRecord())
                ->badge(BillRoom::query()->lastMonthRecord()->count()),

        ];
    }
}
