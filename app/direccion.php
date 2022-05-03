<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class direccion extends Model
{
    public $table = "xt_ger_direcciones";
    protected $fillable = ['id', 'dir_name'];
}
