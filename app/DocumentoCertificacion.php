<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoCertificacion extends Model
{
    public $table = "DocumentType";
    protected $fillable = ['id', 'mainCompanyRut'];
}
