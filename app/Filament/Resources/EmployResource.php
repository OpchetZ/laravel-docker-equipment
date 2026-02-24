<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployResource\Pages;
use App\Filament\Resources\EmployResource\RelationManagers;
use App\Filament\Resources\EmployResource\RelationManagers\EquipmentRelationManager;
use App\Models\Employ;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployResource extends Resource
{
    protected static ?string $model = Employ::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Role')
                    ->nullable()
                    ->label('ตำแหน่ง')
                    ->maxLength(255),
                Forms\Components\TextInput::make('employ_name')
                    ->nullable()
                    ->label('ชื่อพนักงาน')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Role')
                        ->label('ตำแหน่ง')
                        ->searchable(),
                Tables\Columns\TextColumn::make('employ_name')
                        ->label('ชื่อพนักงาน')
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
            EquipmentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmploys::route('/'),
            'create' => Pages\CreateEmploy::route('/create'),
            'edit' => Pages\EditEmploy::route('/{record}/edit'),
        ];
    }
}
