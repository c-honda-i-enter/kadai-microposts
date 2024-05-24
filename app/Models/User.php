<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    // このユーザが所有する投稿（Micropostモデルとの関係を定義）
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    // このユーザに関係するモデルの件数をロード
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers', 'favorites']);
    }
    
    // このユーザーがフォロー中のユーザー（Userモデルとの関係を定義）
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    // このユーザーをフォロー中のユーザー。（Userモデルとの関係を定義）
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    public function follow(int $userId)
    {
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;
        
        if ($exist || $its_me) {
            return false;
        }
        
        else {
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    // $userIdで指定されたユーザーをアンフォローする。
    public function unfollow(int $userId)
    {
        $exist = $this->is_following($userId);
        $its_me = $this->id == $userId;
        
        if ($exist && !$its_me) {
            $this->followings()->detach($userId);
            return true;
        }
        
        else {
            return false;
        }
    }
    
    // 指定された$userIdのユーザーをこのユーザーがフォロー中であるか調べる。フォロー中ならtrueを返す。
    public function is_following(int $userId)
    {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    // このユーザーとフォロー中ユーザーの投稿に絞り込む。
    public function feed_microposts()
    {
        // このユーザーがフォロー中のユーザーのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザーのidもその配列に追加
        $userIds[] = $this->id;
        // それらのユーザーが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    // ユーザがお気に入り登録した投稿
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    // $favoriteIdで指定された投稿をお気に入りに登録する。
    public function favorite(int $favoriteId)
    {
        $exist = $this->is_favorite($favoriteId);
    
        if ($exist) {
            return false;
        }
        
        else {
            $this->favorites()->attach($favoriteId);
            return true;
        }
    }

    // $favoriteIdで指定された投稿をお気に入りから解除する。
    public function unfavorite(int $favoriteId)
    {
        $exist = $this->is_favorite($favoriteId);
    
        if ($exist) {
            $this->favorites()->detach($favoriteId);
            return true;
        }
        
        else {
            return false;
        }
    }
    
    // 指定された$favoriteIDの投稿がお気に入り登録済みか調べる。
    public function is_favorite($micropostId)
    {
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }
    
    // ユーザのお気に入りした投稿に絞り込む。
    public function feed_favorites()
    {
        // ユーザーがお気に入り登録した投稿のidを取得して配列にする
        $favoriteIds = $this->pluck('favorites')->toArray();
        // それらのユーザーが所有する投稿に絞り込む
        return Micropost::whereIn($favoriteId);
    }
}
