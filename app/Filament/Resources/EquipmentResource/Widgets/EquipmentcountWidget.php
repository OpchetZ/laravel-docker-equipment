<?php

namespace App\Filament\Resources\EquipmentResource\Widgets;

use App\Models\Equipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EquipmentcountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $LaptopCount = Equipment::where('category', 'Laptop')->count();
        $PcCount = Equipment::where('category', 'PC')->count();
        $MonitorCount = Equipment::where('category', 'Monitor')->count();
        $PrinterCount = Equipment::where('category', 'Printer')->count();
        
      
        
        
        return [
            Stat::make('อุปกรณ์', Equipment::count())
                ->description('จำนวนอุปกรณ์')
                ->descriptionIcon('heroicon-o-computer-desktop')
                ->color('success'),
            Stat::make('Laptop', $LaptopCount)->description('จำนวนเครื่อง Laptop'),
            Stat::make('PC', $PcCount)->description('จำนวนเครื่อง PC'),
            Stat::make('Monitor', $MonitorCount)->description('จำนวนจอ Monitor'),
            Stat::make('Printer', $PrinterCount)->description('จำนวนเครื่อง Printer'),
            
            
        ];
    }
}
