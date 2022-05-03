<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaDocSso extends Model
{
    public $table = "xt_ssov2_doccats";
    protected $fillable = ['id', 'cat_name','cat_desc'];
}
