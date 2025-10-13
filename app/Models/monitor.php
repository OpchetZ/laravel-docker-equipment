<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class monitor extends Model
{
    use HasFactory;



    protected $table = 'monitors';

    protected $primaryKey = 'id';


    protected $fillable = ['monibrand','monimodel','serialnum','owner_id'];

    public function Owner()
    {
        return $this->belongsTo(Owner::class);
    }
    public function repairs(): MorphMany
{
    return $this->morphMany(Repair::class, 'repairable');
}
}
