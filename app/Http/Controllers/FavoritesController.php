<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    // 投稿をお気に入り登録するアクション
    public function store(string $id)
    {
        // 認証済みユーザーがidの投稿をお気に入りに登録
        \Auth::user()->favorite(intval($id));
        // 前のURLへリダイレクト
        return back();
    }

    // 投稿のお気に入りを解除するアクション
    public function destroy(string $id)
    {
        // 認証済みユーザーがidの投稿のお気に入りを解除
        \Auth::user()->unfavorite(intval($id));
        // 前のURLへリダイレクト
        return back();
    }
}
