<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class categoriaServicio extends Model
{
    public $table = "xt_ger_clasificaciones";
    protected $fillable = ['id', 'class_name'];
}
