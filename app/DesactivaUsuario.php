<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DesactivaUsuario extends Model
{
    public $table = "xt_user_disabled";
    protected $fillable = ['id', 'systemUserId'];

    public function usuarioDatos () {
    	return $this->hasMany('App\DatosUsuarioLogin', 'id', 'systemUserId');
 	}
}
