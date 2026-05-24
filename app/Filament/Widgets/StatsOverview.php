<?php

namespace App\Filament\Widgets;

use App\Models\BillRoom;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Price last month', Number::format(BillRoom::query()->lastMonthRecord()->sum('total_price')))->color('success'),
            Stat::make('Electric consumption last month', BillRoom::query()->lastMonthRecord()->sum('electric_consumption'))->color('success'),
            Stat::make('Water consumption last month', BillRoom::query()->lastMonthRecord()->sum('water_consumption'))->color('success'),
        ];
    }
}
