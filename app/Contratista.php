<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contratista extends Model
{
	public $table = "Company";
    protected $fillable = ['id','rut', 'dv','name'];

    public function certificadoContratista () {
    	return $this->hasMany('App\Certificado', 'companyId', 'id');
 	}
}
