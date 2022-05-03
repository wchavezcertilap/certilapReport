<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    public $table = "xt_ssov2_doctypes";
    protected $fillable = ['id','doc_name'];

    public function estadoDoc () {
    	return $this->hasMany('App\EstadoDocumento','id', 'upld_docid');
 	}

}
