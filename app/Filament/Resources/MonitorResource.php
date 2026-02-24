<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonitorResource\Pages;
use App\Filament\Resources\MonitorResource\RelationManagers;
use App\Filament\Resources\OwnerResource\RelationManagers\MonitorRelationManager;
use App\Models\Monitor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MonitorResource extends Resource
{
    protected static ?string $model = Monitor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('owner_id')
                    ->label('ผู้ใช้งาน')
                    ->relationship('Owner', 'Owner_name')
                    ->searchable(),
                Forms\Components\TextInput::make('monibrand')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('monimodel')
                    ->nullable()
                    ->maxLength(255),
                    Forms\Components\TextInput::make('serialnum')
                    ->nullable()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('monibrand')
                    ->searchable()
                    ->label('Brand'),
                Tables\Columns\TextColumn::make('monimodel')
                    ->searchable()
                    ->label('Model'),
                Tables\Columns\TextColumn::make('serialnum')
                    ->label('Serial number')
                    ->searchable(),
               
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MonitorRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonitors::route('/'),
            'create' => Pages\CreateMonitor::route('/create'),
            'edit' => Pages\EditMonitor::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return ! env('HIDE_EQUIPMENT', true);
    }
}
