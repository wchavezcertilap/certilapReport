<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipoServicio extends Model
{
    public $table = "Servicio";
    protected $fillable = ['id', 'name'];
}
