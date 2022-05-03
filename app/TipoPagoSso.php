<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoPagoSso extends Model
{
    public $table = "xt_sso_payment_mcomp_all_indirectpayact";
    protected $fillable = ['mrut'];

}
