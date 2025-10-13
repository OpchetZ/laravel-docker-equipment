<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OwnerResource\Pages;
use App\Filament\Resources\OwnerResource\RelationManagers;
use App\Filament\Resources\OwnerResource\RelationManagers\MonitorRelationManager;
use App\Filament\Resources\OwnerResource\RelationManagers\PcRelationManager;
use App\Models\monitor;
use App\Models\Owner;
use App\Models\pc;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OwnerResource extends Resource
{
    protected static ?string $model = Owner::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('Dept')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('Location')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('Owner_name')
                    ->nullable()
                    ->maxLength(255),
        //          Select::make('assigned_pcs')
        //     ->label('เลือกคอมพิวเตอร์')
        //     ->multiple()
        //     ->options(fn () => Pc::orderBy('computer_name')->get()
        //         ->mapWithKeys(fn($p) => [$p->id => $p->computer_name ?? ('PC-'.$p->id)])
        //         ->toArray()
        //     )
        //     ->searchable()
        //     ->preload()
        //     ->dehydrated(false) // IMPORTANT: ไม่ให้พยายามบันทึกเป็นคอลัมน์ owners.assigned_pcs
        //     ->reactive()
        //     ->afterStateHydrated(function (Select $component, $state, $record) {
        //         // ถ้าเป็น edit ให้กำหนดค่าเริ่มต้นเป็น pcs ที่มี owner_id = นี้
        //         if ($record?->id) {
        //             $component->state(Pc::where('owner_id', $record->id)->pluck('id')->toArray());
        //         }
        //     }),

        // // Select สำหรับเลือก monitors (multiple) แบบเดียวกัน
        // Select::make('assigned_monitors')
        //     ->label('เลือกจอคอมพิวเตอร์')
        //     ->multiple()
        //     ->options(fn () => Monitor::orderBy('serialnum')->get()
        //         ->mapWithKeys(fn($m) => [$m->id => $m->serialnum ?? ('MON-'.$m->id)])
        //         ->toArray()
        //     )
        //     ->searchable()
        //     ->preload()
        //     ->dehydrated(false)
        //     ->reactive()
        //     ->afterStateHydrated(function (Select $component, $state, $record) {
        //         if ($record?->id) {
        //             $component->state(Monitor::where('owner_id', $record->id)->pluck('id')->toArray());
        //         }
        //     }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('Dept')
                    ->searchable()
                    ->label('Department'),
                Tables\Columns\TextColumn::make('Location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('Owner_name')
                    ->label('Owners name')
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
             PcRelationManager::class,
             MonitorRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOwners::route('/'),
            'create' => Pages\CreateOwner::route('/create'),
            'edit' => Pages\EditOwner::route('/{record}/edit'),
        ];
    }
}
