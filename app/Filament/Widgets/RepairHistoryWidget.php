<?php

namespace App\Filament\Widgets;

use App\Enums\RepairStatus;
use App\Models\Equipment;
use App\Models\Monitor;
use App\Models\Pc;
use App\Models\Repair;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables;
use Filament\Tables\Actions\Action; // Action ทั่วไป
use Filament\Tables\Actions\DeleteAction; // Action สำหรับลบ
use Filament\Tables\Actions\EditAction; // Action สำหรับแก้ไข
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
// ไม่จำเป็นต้องใช้ HasActions trait สำหรับ Header/Row Actions ใน v3 อีกต่อไป

class RepairHistoryWidget extends BaseWidget
{
    protected static ?string $heading = 'ประวัติการซ่อมล่าสุด';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Repair::query()->latest())
            ->paginated([5, 25,'all'])
            ->columns([
                Tables\Columns\TextColumn::make('repairable')
    ->label('อุปกรณ์')
    ->formatStateUsing(function ($record) {
        $device = $record->repairable; // ดึงข้อมูลอุปกรณ์จาก Relation
        
        // ถ้าไม่มีข้อมูลอุปกรณ์เลย ให้โชว์ N/A
        if (! $device) {
            return 'N/A';
        }

        // เช็คจากประเภทแทน (สมมติว่าคุณใช้คอลัมน์ category เก็บประเภท Laptop, Monitor, PC)
        if ($device->category == 'PC') {
            return 'PC: '. ($device->brand_name) .' '. ($device->model ?? 'ไม่รู้โมเดล') . ' Serial Number: ' . ($device->serial_number ?? 'ไม่มีเลขผลิตภัณฑ์');
        } 
        elseif ($device->category == 'Monitor') {
            return 'Monitor: '. ($device->brand_name) .' ' . ($device->model ?? 'ไม่รู้โมเดล') . ' Serial Number: ' . ($device->serial_number ?? $device->brand);
        }
        elseif ($device->category == 'Laptop') {
            return 'Laptop: '. ($device->brand_name) .' ' . ($device->model ?? 'ไม่รู้โมเดล') . ' Serial Number ' . ($device->serial_number ?? 'ไม่มีเลขผลิตภัณฑ์');
        }
        elseif ($device->category == 'Printer') {
            return 'Printer: '. ($device->brand_name) .' ' . ($device->model ?? 'ไม่รู้โมเดล') . ' Serial Number ' . ($device->serial_number ?? 'ไม่มีเลขผลิตภัณฑ์');
        }

        return 'N/A'; // ถ้าเป็นประเภทอื่นที่ไม่เข้าเงื่อนไข
    }),
                // Tables\Columns\TextColumn::make('repairable')
                //     ->label('อุปกรณ์')
                //     ->formatStateUsing(function ($record) {
                //         $device = $record->repairable;
                //         if ($device instanceof Pc) {
                //             return '💻 PC: ' . ($device->model ?? 'ไม่รู้โมเดล') . ' Service_tag:' . ($device->service_tag ?? 'ไม่มีเลขผลิตภัณฑ์');
                //         } elseif ($device instanceof Monitor) {
                //             // แก้ไข: ใช้ serial_number ตามโค้ดเดิมของคุณ
                //             return '🖥️ Monitor: ' . ($device->monimodel ?? 'ไม่รู้โมเดล') . ' Serial Number: ' . ($device->serialnum ?? $device->brand);
                //         }
                //         return 'N/A';
                //     }),
                Tables\Columns\TextColumn::make('description')
                    ->label('อาการ / รายละเอียด')
                    ->wrap()->limit(50),
                Tables\Columns\TextColumn::make('warantee')
                    ->label('ประกัน')
                    ->dateTime('d/m/Y'), 
                Tables\Columns\TextColumn::make('caseno')
                    ->label('Case No'), 
                
                Tables\Columns\TextColumn::make('claimnotidate')
                    ->label('วันที่แจ้งซ่อม')
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('วันที่เสร็จ')
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('status')
                    ->label('สถานะ')
                    ->badge(),
            ])
            ->headerActions([
                // ปุ่ม "สร้าง" ที่มุมบนขวา
                Action::make('createRepair') // <-- ตั้งชื่อให้ไม่ซ้ำใคร
                    ->label('แจ้งซ่อมรายการใหม่')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        // Select::make('repairable_type')
                        //     ->label('ประเภทอุปกรณ์')
                        //     ->options([
                        //         Pc::class => 'Computer (PC)',
                        //         Monitor::class => 'Monitor',
                        //     ])
                        //     ->live()
                        //     ->required(),
                        Select::make('repairable_type')
                            ->label('ประเภทอุปกรณ์')
                            ->options([
                                'Laptop' => 'Laptop',
                                'Monitor' => 'Monitor',
                                'Printer' => 'Printer',
                                'PC' => 'PC',
                            ])
                            ->live()
                            ->required(),
                        Select::make('repairable_id')
                            ->label('อุปกรณ์')
                            ->options(function ($get) {
                                $type = $get('repairable_type');
                                if ($type == 'Laptop'){
                                    return Equipment::where('category', 'Laptop')
                                        ->selectRaw("CONCAT(brand_name,' - ',model, ' - ', serial_number) as full_name, id")
                                        ->pluck('full_name', 'id'); 
                                }
                                elseif($type == 'Pc'){
                                    return Equipment::where('category', 'Pc')
                                        ->selectRaw("CONCAT(brand_name,' - ',model, ' - ', serial_number) as full_name, id")
                                        ->pluck('full_name', 'id'); 
                                }
                                elseif($type == 'Printer'){
                                    return Equipment::where('category', 'Printer')
                                        ->selectRaw("CONCAT(brand_name,' - ',model, ' - ', serial_number) as full_name, id")
                                        ->pluck('full_name', 'id'); 
                                }
                                elseif($type == 'Monitor'){
                                    return Equipment::where('category', 'Monitor')
                                        ->selectRaw("CONCAT(brand_name,' - ',model, ' - ', serial_number) as full_name, id")
                                        ->pluck('full_name', 'id'); 
                                }
                            })
                            ->searchable()
                            ->required(),
                        // Select::make('repairable_id')
                        //     ->label('อุปกรณ์ (Service Tag / Serial No.)')
                        //     ->options(function ($get) {
                        //         $type = $get('repairable_type');
                        //         if ($type === Pc::class) {
                        //             // ถ้าเป็น PC ให้แสดง service_tag
                        //             return Pc::all()->mapWithKeys(function ($pc) {
                        //                 return [$pc->id => ($pc->computer_name ?? '(ไม่มีชื่อเครื่อง)') . '-' .($pc->service_tag ?? '(ไม่มี Service Tag)')];
                        //             });                                }
                        //         if ($type === Monitor::class) {
                        //             // ถ้าเป็น Monitor ให้แสดง serial_number
                        //             return Monitor::all()->mapWithKeys(function ($monitor) {
                        //                 return [$monitor->id => ($monitor->monimodel ?? 'ไม่รู้โมเดล') . '-' .($monitor->serialnum ?? '(ไม่มี Serial Number)')];
                        //           });
                        //         }
                        //         return [];
                        //     })
                        //     ->searchable()
                        //     ->required(),
                        \Filament\Forms\Components\DatePicker::make('warantee') // 📅 ใช้ DatePicker
                            ->label('วันหมดประกัน'), // ไม่ required เพราะใน DB เป็น nullable
                        \Filament\Forms\Components\TextInput::make('caseno') // ✍️ ใช้ TextInput
                            ->label('Case Number'), // ไม่ required เพราะใน DB เป็น nullable
                        \Filament\Forms\Components\DateTimePicker::make('claimnotidate') // ⏰ ใช้ DateTimePicker
                            ->label('วันที่แจ้งเคลม')
                            ->required(), // required เพราะใน DB ตั้งค่าเป็น Not Null
                        Textarea::make('description')
                            ->label('อาการ / รายละเอียด')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // กำหนดสถานะเริ่มต้นให้โดยอัตโนมัติ
                        $data['status'] = RepairStatus::Pending;
                        Repair::create($data);
                    })
                    ->modalWidth('2xl'),
            ])
            ->actions([
                // ปุ่ม Action ในแต่ละแถว
                EditAction::make() // <-- ใช้ EditAction สำเร็จรูป
                    ->form([
                        Textarea::make('description')->required(),

                        \Filament\Forms\Components\DatePicker::make('warantee')->label('วันหมดประกัน'),
                        \Filament\Forms\Components\TextInput::make('caseno')->label('Case Number'),
                        \Filament\Forms\Components\DateTimePicker::make('claimnotidate')->label('วันที่แจ้งเคลม')->required(),
                        \Filament\Forms\Components\DateTimePicker::make('completed_at')->label('วันที่ซ่อมเสร็จ'), // เพิ่มฟิลด์นี้ด้วย
                    
                        ToggleButtons::make('status')->options(RepairStatus::class)->required(),
                    ]),
                DeleteAction::make(), // <-- ใช้ DeleteAction สำเร็จรูป
            ]);
    }
}