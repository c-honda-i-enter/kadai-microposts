<?php

// use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsersController;
use App\Http\Controllers\MicropostsController;
use App\Http\Controllers\UserFollowController;
use App\Http\Controllers\FavoritesController;

Route::get('/', [MicropostsController::class, 'index']);

Route::get('/dashboard',[MicropostsController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::prefix('users/{id}')->group(function () {
        Route::post('follow', [UserFollowController::class, 'store'])->name('user.follow');
        Route::delete('unfollow', [UserFollowController::class, 'destroy'])->name('user.unfollow');
        Route::get('followings', [UsersController::class, 'followings'])->name('users.followings');
        Route::get('followers', [UsersController::class, 'followers'])->name('users.followers');
    });
    
    Route::prefix('microposts/{id}')->group(function() {
        Route::post('favorite', [FavoritesController::class, 'store'])->name('user.favorite');
        Route::delete('unfavorite', [FavoritesController::class, 'destroy'])->name('user.unfavorite');
        Route::get('favorites', [UsersController::class, 'favorites'])->name('user.favorites');
        // Route::get('users/{id}/favorites', [UsersController::class, 'favorites'])->name('users.favorites');
    });
    
    Route::resource('users', UsersController::class, ['only' => ['index', 'show']]);

    Route::resource('microposts', MicropostsController::class, ['only' => ['store', 'destroy']]);
    
});
    
require __DIR__.'/auth.php';