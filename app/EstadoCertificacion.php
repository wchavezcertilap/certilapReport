<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EstadoCertificacion extends Model
{
   	public $table = "EstadoCertificacion";
     protected $fillable = ['id','nombre','estado'];
}
