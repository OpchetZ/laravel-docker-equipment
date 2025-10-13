<?php

namespace App\Filament\Resources\OwnerResource\RelationManagers;

use App\Models\pc;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class PcRelationManager extends RelationManager
{
    protected static string $relationship = 'pc';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('owner_id')
                    ->label('ผู้ใช้งาน')
                    ->relationship('Owner', 'Owner_name')
                    ->searchable(),
                Forms\Components\TextInput::make('computer_name')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ipconfig')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('os')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('patch')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('mc')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('brand')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('model')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('service_tag')
                    ->nullable()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('computer_name')
            ->columns([
                Tables\Columns\TextColumn::make('computer_name')
                        ->label('Computer name')
                        ->searchable(),
                Tables\Columns\TextColumn::make('brand')
                        ->searchable(),
                Tables\Columns\TextColumn::make('model')
                        ->label('Model')
                        ->searchable(),
                Tables\Columns\TextColumn::make('ipconfig')
                        ->label('IP Address')
                        ->copyable(fn ($record) => $record->ipconfig)
                        ->searchable(),
                Tables\Columns\TextColumn::make('service_tag')
                        ->label('Service tag')
                        ->copyable(fn ($record) => $record->service_tag)
                        ->searchable(),
                Tables\Columns\TextColumn::make('os')
                        ->label('OS')
                        ->searchable(),
                Tables\Columns\TextColumn::make('patch')
                        ->label('Patch')
                        ->searchable(),
                Tables\Columns\TextColumn::make('mc')
                        ->label('Microsoft')
                        ->searchable(),
                Tables\Columns\TextColumn::make('type')
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
                        Forms\Components\Select::make('computer_name')
                            ->label('เลือก PC')
                            ->options(Pc::whereNull('owner_id')->get()->mapWithKeys(fn ($pc) => [$pc->id => ($pc->computer_name ?? '(ไม่ระบุ)') . ' - ' . ($pc->service_tag ?? '(ไม่ระบุ)')]))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, $record, $livewire) {
                        $owner = $livewire->owner ?? $livewire->getOwnerRecord();
                        $pc = Pc::find($data['computer_name']);
                        if ($pc) {
                            $pc->owner_id = $owner->id;
                            $pc->save();
                        }
                    })
                    ->color('success'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('check_warranty')
    ->label('เช็คประกัน')
    ->url(function ($record) {
        if (strtolower($record->brand) === 'dell') {
            return "https://www.dell.com/support/home/th-th/product-support/servicetag/{$record->service_tag}/overview";
        } elseif (strtolower($record->brand) === 'asus') {
            return "https://www.asus.com/th/support/warranty-status-inquiry/";
        } elseif (strtolower($record->brand) === 'hp') {
            $hpModels = [
                'ProBook 440' => "https://support.hp.com/th-th/warrantyresult/hp-probook-440-14-inch-g9-notebook-pc/2101000453/model/2101000459?sku=6L303PA&serialnumber={$record->service_tag}",
                'Pro Mini 400 G9' => "https://support.hp.com/th-th/warrantyresult/hp-pro-mini-400-g9-desktop-pc/2101054031/model/2101054061?sku=4G4N7AV&serialnumber={$record->service_tag}",
            ];
            return $hpModels[$record->model] ?? "https://support.hp.com/th-th/checkwarranty";
        }

        return null;
    })
    ->openUrlInNewTab()
    ->icon('heroicon-o-globe-alt'),
    Tables\Actions\Action::make('ping')
                    ->label('Ping')
                    ->icon('heroicon-o-wifi')
                    ->color('info')
                    ->modalHeading('Ping Result')
                    ->requiresConfirmation()
                    ->modalContent(function ($record) {
                       $ip = $record->ipconfig;

    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return new HtmlString("<div style='color:red;'>Invalid IP Address</div>");
    }

    //command Linux container เป็น Linux
    
    $command = sprintf("ping -c 2 -W 2 %s", escapeshellarg($ip));

    $output = null;
    $status = null;

    exec($command, $output, $status);

    $message = $status === 0
        ? "✅ **Success:** $ip is reachable."
        : "❌ **Failed:** $ip is unreachable.";

    $outputText = implode("\n", $output);

    return new HtmlString(
        "<pre style='max-height:400px; overflow:auto; background:#111; color:#0f0; padding:10px; border-radius:8px;'>"
        . e($message . "\n\n" . $outputText) .
        "</pre>"
    );
    }),
                Tables\Actions\DeleteAction::make()
                    ->action(function ($record) {
                        // เมื่อกดลบ จะ unassign เครื่องออกจาก owner แทนการลบ record
                        $record->owner_id = null;
                        $record->save();
                    })
                    ->label('เอาเครื่องออก')
                    ->color('danger'),
            ])
            ->actionsPosition(ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->action(function ($record) {
                        // เมื่อกดลบ จะ unassign เครื่องออกจาก owner แทนการลบ record
                        $record->owner_id = null;
                        $record->save();
                    })
                    ->label('เอาเครื่องออก')
                    ->color('warning'),
                ]),
            ]);
    }
}
