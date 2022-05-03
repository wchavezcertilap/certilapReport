<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaConfSso extends Model
{
    public $table = "xt_ssov2_configs_cargos";
    protected $fillable = ['cfg_id', 'cargo_id'];
}
