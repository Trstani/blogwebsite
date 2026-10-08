<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'bio', 'avatar', 'avatar_public_id', 'slug', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // User punya banyak articles
    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    // User punya banyak comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // User punya banyak liked articles (through article_likes)
    public function likedArticles()
    {
        return $this->belongsToMany(Article::class, 'article_likes', 'user_id', 'article_id')
            ->withTimestamps();
    }

    // User punya banyak bookmarked articles (through article_bookmarks)
    public function bookmarkedArticles()
    {
        return $this->belongsToMany(Article::class, 'article_bookmarks', 'user_id', 'article_id')
            ->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->role === 'super_admin';
    }

    public function isWriter(): bool
    {
        return $this->role === 'writer';
    }
}
