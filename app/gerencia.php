<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class gerencia extends Model
{
    public $table = "xt_ger_gerencias";
    protected $fillable = ['id', 'ger_name'];
}
