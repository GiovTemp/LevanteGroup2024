<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Argument extends Model
{
    //
    protected $fillable = [
        'title','content','to_generate'
    ];

    public $timestamps = false;
}
