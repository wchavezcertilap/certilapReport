<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Periodo extends Model
{
    public $table = "Period";
    protected $fillable = ['id', 'monthId','year'];

    public function mes () {
    	return $this->hasMany('App\Month', 'id', 'monthId');
 	}



 
}
