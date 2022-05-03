<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoRechazdo extends Model
{
    public $table = "documentoRechazado";
    protected $fillable = ['id','companyId'];
}
