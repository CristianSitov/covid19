<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $fillable = [
        'capture_date',
        'country',
        'confirmed',
        'totals_confirmed',
        'avg3_confirmed',
        'avg7_confirmed',
        'confirmed_million',
        'deaths',
        'totals_deaths',
        'avg3_deaths',
        'avg7_deaths',
        'deaths_million',
        'population',
        'created_at',
        'updated_at',
    ];
}
