<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    public $table = "Request";
    protected $fillable = ['id','companyId'];
}
