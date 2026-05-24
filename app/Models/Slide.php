<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $fillable = [
        'tagline',
        'title',
        'subtitle',
        'link',
        'image',
        'status',
    ];
}
