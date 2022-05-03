<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class depositoSolictud extends Model
{
    public $table = "xt_request_deposit";
    protected $fillable = ['request_id'];
}
