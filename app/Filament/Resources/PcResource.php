<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OwnerResource\RelationManagers\PcRelationManager;
use App\Filament\Resources\PcResource\Pages;
use App\Filament\Resources\PcResource\RelationManagers;
use App\Models\Pc;
use Webbingbrasil\FilamentCopyActions\Tables\Actions\CopyAction;
use Spatie\Ping\Ping;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Filament\Tables\Enums\RecordActionsPosition;
use Illuminate\Database\Eloquent\Relations\Relation;

class PcResource extends Resource
{
    protected static ?string $model = Pc::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('computer_name')
                        ->label('Computer name')
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
                Tables\Columns\TextColumn::make('brand')
                        ->searchable(),
                Tables\Columns\TextColumn::make('model')
                        ->label('Model')
                        ->searchable(),
                
                
            ])
            ->filters([
                //
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
            ])
            ->actionsPosition(ActionsPosition::BeforeColumns)
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPcs::route('/'),
            'create' => Pages\CreatePc::route('/create'),
            'edit' => Pages\EditPc::route('/{record}/edit'),
        ];
    }
}
