<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoDocumento extends Model
{
   	public $table = "xt_ssov2_header_uploads";
    protected $fillable = ['id','upld_sso_id'];


 	public function nombreDocumento() {
       return $this->belongsTo('App\Documento', 'id', 'upld_docid');
    }
}
