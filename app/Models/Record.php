<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $fillable = [
        'capture_date',
        'country',
        'confirmed',
        'deaths',
        'population',
        'created_at',
        'updated_at',
    ];
}
