<?php

namespace App\Models;

use App\Enums\RepairStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class repair extends Model
{
    use HasFactory;
    protected $guarded = [];

    // กำหนดให้ Laravel แปลงค่า status เป็น Enum อัตโนมัติ
    protected $casts = [
        'status' => RepairStatus::class,
    ];

    public function repairable(): MorphTo
    {
        return $this->morphTo();
    }
}
