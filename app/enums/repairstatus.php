<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum RepairStatus: string implements HasColor, HasIcon, HasLabel
{
    case Pending = 'pending';
    case InProgress = 'in-progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Pending => 'รอดำเนินการ',
            self::InProgress => 'กำลังซ่อม',
            self::Completed => 'ซ่อมเสร็จ',
            self::Cancelled => 'ยกเลิก',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::InProgress => 'primary',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::InProgress => 'heroicon-o-arrow-path',
            self::Completed => 'heroicon-o-check-badge',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }
}