<?php

namespace App\Filament\Resources\OwnerResource\Widgets;

use App\Filament\Resources\OwnerResource;
use App\Models\pc;
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


class OwnerPcMonitorTable extends BaseWidget
{
     protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'TRCLIST';
     
    public function table(Table $table): Table
    {
        return $table
            ->query(OwnerResource::getEloquentQuery())
            ->paginated([5, 50, 100, 'all'])
            ->columns([
            Tables\Columns\TextColumn::make('Dept')
                ->label('แผนก')
                ->searchable(),
            Tables\Columns\TextColumn::make('Location')
                ->searchable(),
            Tables\Columns\TextColumn::make('Owner_name')
                ->label('ชื่อผู้ใช้')
                ->searchable(),
            
               
            Tables\Columns\ViewColumn::make('details')
                ->label('รายละเอียด')
                ->view('filament.tables.columns.owner-equipment-details'),
            ])
           
            ->actions([
              Action::make('open')
                ->label('แก้ไข')
                ->icon('heroicon-o-pencil')
                ->url(fn ($record): string => OwnerResource::getUrl('edit', ['record' => $record])),        

            ])
            ->actionsPosition(ActionsPosition::BeforeColumns);

    }
    
}
