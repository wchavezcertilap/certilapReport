<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public $table = "Region";
    protected $fillable = ['id','name'];
}
