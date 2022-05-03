<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsuarioPrincipal extends Model
{
    public $table = "SystemUserMainCompany";
    protected $fillable = ['id', 'systemUserId','mainCompanyRut'];

    public function usuarioDatos () {
    	return $this->hasMany('App\DatosUsuarioLogin', 'id', 'systemUserId');
 	}
}
