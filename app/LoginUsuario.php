<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginUsuario extends Model
{
    public $table = "xt_user_logins";
    protected $fillable = ['id', 'systemUserId'];

    public function usuarioDatos () {
    	return $this->hasMany('App\DatosUsuarioLogin', 'id', 'systemUserId');
 	}
}
