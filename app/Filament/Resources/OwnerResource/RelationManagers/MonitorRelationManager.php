<?php

namespace App\Filament\Resources\OwnerResource\RelationManagers;

use App\Models\monitor;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MonitorRelationManager extends RelationManager
{
    protected static string $relationship = 'monitor';

    public function form(Form $form): Form
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('serialnum')
            ->columns([
                Tables\Columns\TextColumn::make('monibrand')
                    ->label('Brand'),
                Tables\Columns\TextColumn::make('monimodel')
                    ->label('Model'),
                Tables\Columns\TextColumn::make('serialnum')
                    ->label('Serial number'),
                
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
                        Forms\Components\Select::make('serialnum')
                            ->label('เลือก Monitor')
                            ->options(monitor::whereNull('owner_id')->get()->mapWithKeys(fn ($pc) => [$pc->id => ($pc->monibrand ?? '(ไม่ระบุ)') . '-' . ($pc->serialnum ?? '(ไม่ระบุ)')]))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, $record, $livewire) {
                        $owner = $livewire->owner ?? $livewire->getOwnerRecord();
                        $moni = monitor::find($data['serialnum']);
                        if ($moni) {
                            $moni->owner_id = $owner->id;
                            $moni->save();
                        }
                    })
                    ->color('success'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->action(function ($record) {
                        $record->owner_id = null;
                        $record->save();
                    })
                    ->label('เอาเครื่องออก')
                    ->color('warning'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->action(function ($record) {
                        $record->owner_id = null;
                        $record->save();
                    })
                    ->label('เอาเครื่องออก')
                    ->color('warning'),
                ]),
            ]);
    }
}
