<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyWebPay extends Model
{
    public $table = "CompanyWebpay";
    protected $fillable = ['refid'];
}
