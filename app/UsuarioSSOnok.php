<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsuarioSSOnok extends Model
{
   	public $table = "xt_user_ssocfg";
     protected $fillable = ['user_id','user_sso_isaprob','user_sso_isnoc'];
}
