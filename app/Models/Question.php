<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'domain', 'code', 'question', 'options', 'max_points', 'order'
    ];

    // AJOUTE CETTE LIGNE - Conversion automatique array ↔ JSON
    protected $casts = [
        'options' => 'array',
    ];
}
