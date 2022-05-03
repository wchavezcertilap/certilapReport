<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class trabajadorFactura extends Model
{
    public $table = "xt_ssov2_payment_invoice_pos";
    protected $fillable = ['invc_id','pos_id'];
}
