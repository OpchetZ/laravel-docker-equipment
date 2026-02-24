<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentResource\Pages;
use App\Filament\Resources\EquipmentResource\RelationManagers;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('brand_name')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('model')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\Select::make('category')
                    ->options([
                        'Laptop' => 'Laptop',
                        'Monitor' => 'Monitor',
                        'Printer' => 'Printer',
                        'PC' => 'PC',
                    ])
                    ->nullable(),
                Forms\Components\TextInput::make('serial_number')
                    ->nullable()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand_name')
                        ->label('Brand')
                        ->searchable(),
                Tables\Columns\TextColumn::make('model')
                        ->label('Model')
                        ->searchable(),
                Tables\Columns\TextColumn::make('category')
                        ->label('Category')
                        ->searchable(),
                Tables\Columns\TextColumn::make('serial_number')
                        ->label('Serial Number')
                        ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEquipment::route('/'),
            'create' => Pages\CreateEquipment::route('/create'),
            'edit' => Pages\EditEquipment::route('/{record}/edit'),
        ];
    }
    
}
