<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class trabajadorSSO extends Model
{
    public $table = "xt_ssov2_header_worker";
    protected $fillable = ['id', 'sso_id'];
}
