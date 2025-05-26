<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    protected $fillable = [
        'xml_id',
        'name',
        'status',
        'number',
        'update'
    ];
}
