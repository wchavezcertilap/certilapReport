<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CargoSSO extends Model
{
    public $table = "xt_ssov2_cargos";
    protected $fillable = ['id', 'cargo_name'];
}
