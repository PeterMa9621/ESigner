<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SignaturePosition extends Model
{
    protected $fillable = ['x', 'y', 'page'];

    public $timestamps = false;
}
