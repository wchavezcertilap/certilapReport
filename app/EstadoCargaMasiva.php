<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoCargaMasiva extends Model
{
    public $table = "cargaMasivaEstatus";
    protected $fillable = ['id','companyId'];
}
