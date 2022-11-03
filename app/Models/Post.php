<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'reposted_id',
    ];

    protected $withCount = [
        'comments',
    ];

    // жадная загрузка - сразу делается запрос к изображениям и к таблице лайков
    protected $with = [
        'user',
        'image',
        'likedUsers',
        'repostedPost'
    ];

    protected $date = ['created_at'];

    public function image()
    {
        return $this->hasOne(PostImage::class, 'post_id', 'id')->whereNotNull('post_id');
    }

    public function getDateAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Связь с пользователями, которые поставили лайк посту
     */
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'liked_posts', 'post_id', 'user_id');
    }

    /**
     * Связь поста с репостом
     */
    public function repostedPost()
    {
        return $this->belongsTo(Post::class, 'reposted_id', 'id');
    }

    /**
     * Сколькими постами был сделан репост данного поста
     */
    public function repostedByPosts()
    {
        return $this->hasMany(Post::class, 'reposted_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
