<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $fillable = [
        'user_id', 'company_name', 'sector', 'employees_count',
        'scores', 'total_score', 'status'
    ];

    protected $casts = [
        'scores' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
