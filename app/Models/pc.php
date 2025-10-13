<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Ping\Ping;

class pc extends Model
{
    use HasFactory;



    protected $table = 'pcs';

    protected $primaryKey = 'id';


    protected $fillable = ['computer_name','ipconfig','os','patch','mc','type','brand','model','service_tag','owner_id'];

    public function Owner()
    {
        return $this->belongsTo(Owner::class);
    }
    public function repairs(): MorphMany
{
    return $this->morphMany(Repair::class, 'repairable');
}

}

