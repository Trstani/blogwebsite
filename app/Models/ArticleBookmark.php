<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleBookmark extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'article_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'article_bookmarks';

    /**
     * Get the user that owns this bookmark.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the article that was bookmarked.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
