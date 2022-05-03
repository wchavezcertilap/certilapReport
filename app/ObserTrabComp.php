<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObserTrabComp extends Model
{
    public $table = "obserTrabComp";
    protected $fillable = ['id','idCompany'];
}
