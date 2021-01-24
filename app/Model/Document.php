<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['name', 'path', 'signed_path', 'is_signed', 'signature_position_id', 'is_deleted', 'width', 'height', 'numPages'];

    protected $attributes = [
        'is_signed' => false,
        'is_deleted' => false
    ];
}
