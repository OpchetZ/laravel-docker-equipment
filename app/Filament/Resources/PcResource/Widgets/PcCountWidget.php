<?php

namespace App\Filament\Resources\PcResource\Widgets;

use App\Models\pc;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PcCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $dellCount = pc::where('brand', 'Dell')->count();
        $hpCount   = pc::where('brand', 'HP')->count();
        $asusCount = pc::where('brand', 'ASUS')->count();
        $intelCount = pc::where('brand', 'Intel')->count();
        $vspaceCount = pc::where('brand', 'Vspace')->count();
        return [
            Stat::make('Computers', pc::count())
                ->description('จำนวนคอมพิวเตอร์')
                ->descriptionIcon('heroicon-o-computer-desktop')
                ->color('success'),
            Stat::make('Dell', $dellCount)->description('จำนวนเครื่อง Dell'),
            Stat::make('HP', $hpCount)->description('จำนวนเครื่อง HP'),
            Stat::make('Asus', $asusCount)->description('จำนวนเครื่อง Asus'),
            Stat::make('Intel', $intelCount)->description('จำนวนเครื่อง Intel'),
            Stat::make('Vspace', $vspaceCount)->description('จำนวนเครื่อง Vspace'),
            
        ];
    }
}
