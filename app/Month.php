<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Month extends Model
{
    public $table = "Month";
    protected $fillable = ['id','name','number'];

    public function periodo () {
    	return $this->belongsTO('App\Periodo', 'id', 'monthId');
 	}

 	

}
