<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Mediable;

class Landing extends Model
{
    protected $fillable = ['landingData'];
    protected $casts = [
        'landingData' => 'json',
    ];
    use HasFactory;
    use Mediable;

}
