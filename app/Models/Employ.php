<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employ extends Model
{
    use HasFactory;

    protected $table = 'employs';
    protected $primaryKey = 'id';

    protected $fillable = ['Role','employ_name'];

    public function Equipment()
    {
        return $this->hasMany(Equipment::class);
    }
    
}
