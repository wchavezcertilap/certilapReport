<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CargoCateDoc extends Model
{
    public $table = "xt_ssov2_configs_cargos_cats_docs";
    protected $fillable = ['cfg_id', 'cargo_id','cat_id','doc_id'];
}
