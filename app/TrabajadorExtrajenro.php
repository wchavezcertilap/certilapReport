<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrabajadorExtrajenro extends Model
{
    public $table = "PlanillaExtranjeros";
    protected $fillable = ['id', 'rut','dv'];
}
