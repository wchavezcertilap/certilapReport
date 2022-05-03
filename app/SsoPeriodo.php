<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SsoPeriodo extends Model
{
    public $timestamps = false;
    public $table = "xt_ssov2_periodo";
    protected $fillable = ['id', 'sso_id'];
}
