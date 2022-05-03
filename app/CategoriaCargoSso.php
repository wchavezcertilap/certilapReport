<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaCargoSso extends Model
{
    public $table = "xt_ssov2_configs_cargos_cats";
    protected $fillable = ['cfg_id', 'cargo_id','cat_id'];
}
