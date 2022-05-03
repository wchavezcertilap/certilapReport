<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipoEmpresa extends Model
{
    public $table = "CompanyType";
    protected $fillable = ['id', 'name'];
}
