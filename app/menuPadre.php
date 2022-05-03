<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class menuPadre extends Model
{
    //
    public $table = "menuPadre";
     protected $fillable = ['nombreMenu', 'estado', 'perfilUsuario','created_at'];
}
