<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColumnaExcel extends Model
{
    public $table = "ColumnaExcel";
    protected $fillable = ['id','nombreColumna', 'estado'];
}
