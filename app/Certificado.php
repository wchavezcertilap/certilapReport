<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    public $table = "Certificate";
    protected $fillable = ['id','companyId'];

    public function cargaCertificadoContratista () {
    	return $this->belongsTo('App\Contratista', 'companyId', 'id');
 	}
}
