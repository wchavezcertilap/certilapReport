<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DatosUsuarioLogin extends Model
{
    public $table = "SystemUser";
    protected $fillable = ['id','name', 'type'];

    public function cargaUsuarioContratista () {
    	return $this->belongsTo('App\UsuarioContratista', 'id', 'systemUserId');
 	}

 	public function cargaUsuarioPrincipal () {
    	return $this->belongsTo('App\UsuarioPrincipal', 'id', 'systemUserId');
 	}
}
