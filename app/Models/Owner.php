<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;



    protected $table = 'owners';

    protected $primaryKey = 'id';


    protected $fillable = ['Dept','Location','Owner_name'];

    public function monitor()
    {
        return $this->hasMany(monitor::class);
    }
    public function pc()
    {
        return $this->hasMany(pc::class);
    }
}
