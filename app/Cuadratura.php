<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cuadratura extends Model
{
    public $table = "Quadrature";
    protected $fillable = ['id','companyId'];
}
