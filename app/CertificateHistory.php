<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CertificateHistory extends Model
{
   	public $table = "CertificateHistory";
     protected $fillable = ['id','companyId'];
}
