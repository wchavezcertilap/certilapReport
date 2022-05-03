<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comuna extends Model
{
    public $table = "Town";
    protected $fillable = ['id','name'];
}
