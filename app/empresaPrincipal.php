<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class empresaPrincipal extends Model
{
    public $table = "mainCompany";
    protected $fillable = ['id','rut', 'dv','name'];
}


