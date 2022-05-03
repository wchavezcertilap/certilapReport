<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsuarioContratista extends Model
{
    public $table = "SystemUserCompany";
    protected $fillable = ['id', 'systemUserId','companyRut'];

    public function usuarioDatos () {
    	return $this->hasMany('App\DatosUsuarioLogin', 'id', 'systemUserId');
 	}
}
