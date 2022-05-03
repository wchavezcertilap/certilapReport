<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FolioSso extends Model
{
	public $timestamps = false;
    public $table = "xt_ssov2_header";
    protected $fillable = ['id', 'sso_comp_rut','sso_mcomp_rut'];
}
