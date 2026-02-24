<?php

namespace App\Filament\Resources\EmployResource\Widgets;

use App\Filament\Resources\EmployResource;
use App\Models\Equipment;
use Filament\Tables;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;


class EmployEquipTable extends BaseWidget
{
     protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Employee';
     
    public function table(Table $table): Table
    {
        return $table
            ->query(EmployResource::getEloquentQuery())
            ->paginated([5, 50, 100, 'all'])
            ->columns([
            Tables\Columns\TextColumn::make('employ_name')
                ->label('ชื่อพนักงาน')
                ->searchable(),
            Tables\Columns\TextColumn::make('Role')
                ->label('ตำแหน่ง')
                ->searchable(),
            
               
            Tables\Columns\ViewColumn::make('details')
                ->label('อุปกรณ์ที่ใช้')
                ->view('filament.tables.columns.employ-equip-details'),
            ])
           
            ->actions([
              Action::make('open')
                ->label('แก้ไข')
                ->icon('heroicon-o-pencil')
                ->url(fn ($record): string => EmployResource::getUrl('edit', ['record' => $record])),        

            ])
            ->actionsPosition(ActionsPosition::BeforeColumns);

    }
    
}
