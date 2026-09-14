<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleSection extends Model
{
    use HasFactory;

    protected $fillable = ['article_id', 'type', 'content', 'order', 'image_public_id'];

    // Section punya 1 article
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
