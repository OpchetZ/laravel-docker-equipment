<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';
    protected $primaryKey = 'id';

    protected $fillable = ['brand_name','model','category','serial_number','employ_id'];

    // public function Owner()
    // {
    //     return $this->belongsTo(Owner::class);
    // }
    public function repairs(): MorphMany
    {
    return $this->morphMany(repair::class, 'repairable');
    }
    public function Employ()
    {
        return $this->belongsTo(Employ::class);
    }
}
