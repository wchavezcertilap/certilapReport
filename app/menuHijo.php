<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class menuHijo extends Model
{
    public $table = "menuHijo";
     protected $fillable = ['nombreMenuHijo', 'idMenuPadre','perfilUsuario', 'estado'];
}
