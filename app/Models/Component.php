<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID';
    protected $fillable = ['name', 'type', 'componentData'];
    protected $casts = ['componentData' => 'array'];
}
