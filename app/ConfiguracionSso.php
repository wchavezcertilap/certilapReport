<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionSso extends Model
{
    public $table = "xt_ssov2_configs";
    protected $fillable = ['id', 'cfg_mcomp_rut','cfg_mcomp_dv','cfg_mcomp_name'];
}
