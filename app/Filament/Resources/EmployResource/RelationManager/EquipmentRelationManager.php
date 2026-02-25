<?php

namespace App\Filament\Resources\EmployResource\RelationManagers;

use App\Models\Equipment;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class EquipmentRelationManager extends RelationManager
{
    protected static string $relationship = 'Equipment';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employ_id')
                    ->label('พนักงาน')
                    ->relationship('Employ', 'employ_name')
                    ->searchable(),
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('serialnum')
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
                ActionsAction::make('attachExisting')
                    ->label('เลือกเครื่องที่มีอยู่')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        Forms\Components\Select::make('serial_number')
                            ->label('เลือก อุปกรณ์')
                            ->options(Equipment::whereNull('employ_id')->get()->mapWithKeys(fn ($pc) => [$pc->id => ($pc->brand_name ?? '(ไม่ระบุ)') . '-' . ($pc->serial_number ?? '(ไม่ระบุ)')]))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, $record, $livewire) {
                        $employ = $livewire->employ ?? $livewire->getOwnerRecord();
                        $equ = Equipment::find($data['serial_number']);
                        if ($equ) {
                            $equ->employ_id = $employ->id;
                            $equ->save();
                        }
                    })
                    ->color('success'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->action(function ($record) {
                        $record->employ_id = null;
                        $record->save();
                    })
                    ->label('เอาเครื่องออก')
                    ->color('warning'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->action(function ($record) {
                        $record->employ_id = null;
                        $record->save();
                    })
                    ->label('เอาเครื่องออก')
                    ->color('warning'),
                ]),
            ]);
    }
}
