<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Micropost extends Model
{
    use HasFactory;
    
    protected $fillable = ['content'];
    
    // この投稿を所有するユーザー（ Userモデルとの関係を定義）
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // このお気に入りされた投稿を所有するユーザー
    public function favorite_users()
    {
        return $this->belongsToMany(User::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
}
