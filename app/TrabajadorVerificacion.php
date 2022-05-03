<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrabajadorVerificacion extends Model
{
    public $table = "Worker";
    protected $fillable = ['id', 'rut','dv'];
}
