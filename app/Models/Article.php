<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover_image',
        'cover_image_public_id',
        'category_id',
        'author_id',
        'status',
        'views',
        'published_at',
    ];

    // Otomatis bikin slug dari title
    public static function boot()
    {
        parent::boot();
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    // Artikel punya banyak sections
    public function sections()
    {
        return $this->hasMany(ArticleSection::class)->orderBy('order');
    }

    // Artikel punya banyak comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Artikel punya category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Artikel punya author (user)
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Scope: cuma article yang published
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Scope: urutkan dari terbaru
    public function scopeLatest($query)
    {
        return $query->latest();
    }

    // Scope: urutkan dari views terbanyak
    public function scopePopular($query)
    {
        return $query->orderByDesc('views');
    }
}
