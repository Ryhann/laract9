<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\OuterController;
use Illuminate\Support\Facades\Route;

// ..
// dont use PREFIX for this main routes!
// ..

// outer class

Route::controller(OuterController::class)->name('outer.')->group(
    function () {
        Route::get('/', 'index')->name('main')->middleware('throttle:20,1');
        Route::get('/posts', 'PostsAll')->name('posts');
        Route::post('/posts', 'MorePosts')->name('posts.more');
        Route::get('/post/{id}', 'find')->name('byId');
    }
);

Route::controller(AuthorController::class)->name('author.')->middleware('throttle:30,1')->group(
    function () {
        Route::get('/author/{author}', 'profile')->name('profile');
        Route::get('/cuypeople/status', 'userOnlineStatus')->name('status');
    }
);

// user dashboard authorized grup

Route::middleware(['auth', 'verified'])->group(function() {
    Route::controller(DashboardController::class)->name('dash.')->group(
        function () {
            Route::get('/dashboard', 'index')->name('main');
            Route::get('/dashboard/notif', 'notification')->name('notif');
            Route::get('/dashboard/manage-posts', 'manage_posts')->name('manage.posts');
            Route::post('/dashboard/photo', 'update_photo')->name('update.photo')->middleware('isValidUser');
            Route::put('/dashboard/update-username', 'update_username')->name('update.username')->middleware('isValidUser');
        }
    );

    //user dashboard posts
    Route::controller(PostsController::class)->name('posts.')->group(
        function () {
            Route::get('/dashboard/manage-posts/posts', 'show')->name('main');
            Route::get('/dashboard/manage-posts/posts/create', 'create')->name('create');
            Route::post('/dashboard/manage-posts/posts', 'store')->name('store')->middleware('isValidUser');
            Route::post('/dashboard/manage-posts/posts/delete', 'destroy')->name('remove')->middleware('isValidUser');
            Route::post('/post/comment', 'storeComment')->name('storeComment')->middleware('isValidUser');
            Route::post('/post/like/love', 'storeLike')->name('storeLike')->middleware('isValidUser');
        }
    );

});


require __DIR__ . '/auth.php';
