<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdenComContratista extends Model
{
    public $table = "xt_oc_groups_pos";
    protected $fillable = ['id', 'ocg_id'];
}
