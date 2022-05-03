<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tipoUsuario extends Model
{
    //
    public $table = "tipoUsario";
    protected $fillable = ['id', 'nombreTipoUsuario','estado', 'perfilUsuario'];

    
}
