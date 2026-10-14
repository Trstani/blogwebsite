<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'created_by'];
    
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Tag punya banyak articles
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_tag');
    }
}
