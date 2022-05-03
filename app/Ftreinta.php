<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ftreinta extends Model
{
    public $table = "xt_form_f30";
    protected $fillable = ['id', 'form_mainCompany','form_company'];
}
