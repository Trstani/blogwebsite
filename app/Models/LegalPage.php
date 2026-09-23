<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalPage extends Model
{
    protected $fillable = [
        'type',
        'title',
        'content',
        'updated_by',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}