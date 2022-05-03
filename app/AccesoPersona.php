<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccesoPersona extends Model
{
    public $table = "acceso_persona";
    protected $fillable = ['ACC_ID', 'ACC_RUT'];
}
