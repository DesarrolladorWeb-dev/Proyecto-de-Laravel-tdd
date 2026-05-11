<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function plates(){
        //la tabla plate tiene un campo llamado restauran_id y con eso lo reconoce
        //tiene muchos plate un restaurante 
        return $this->hasMany(Plate::class);
    }
}
