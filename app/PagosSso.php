<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagosSso extends Model
{
    public $table = "xt_ssov2_payment_invoice_header";
    protected $fillable = ['id','sso_id'];
}
