<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/pets/{pet}', [HomeController::class, 'petShow'])->whereNumber('pet')->name('pets.show');

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginWithPassword'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/settings', [AuthController::class, 'settings'])->name('settings');
    Route::get('/user/passkeys/options', [AuthController::class, 'passkeyOptions'])->name('user.passkeys.options');
    Route::post('/user/passkeys', [AuthController::class, 'storePasskey'])->name('user.passkeys.store');
    Route::delete('/user/passkeys/{passkeyId}', [AuthController::class, 'deletePasskey'])->name('user.passkeys.delete');

    Route::get('/pets', [HomeController::class, 'pets'])->name('pets.index');
    Route::get('/pets/create', [HomeController::class, 'petCreate'])->name('pets.create');
    Route::post('/pets', [HomeController::class, 'petStore'])->name('pets.store');
    Route::get('/pets/{pet}/edit', [HomeController::class, 'petEdit'])->name('pets.edit');
    Route::put('/pets/{pet}', [HomeController::class, 'petUpdate'])->name('pets.update');
    Route::delete('/pets/{pet}', [HomeController::class, 'petDestroy'])->name('pets.destroy');

    Route::get('/pets/{pet}/records/create', [HomeController::class, 'petRecordCreate'])->name('pets.records.create');
    Route::post('/pets/{pet}/records', [HomeController::class, 'petRecordStore'])->name('pets.records.store');
    Route::get('/pets/{pet}/records/{record}/edit', [HomeController::class, 'petRecordEdit'])->name('pets.records.edit');
    Route::put('/pets/{pet}/records/{record}', [HomeController::class, 'petRecordUpdate'])->name('pets.records.update');
    Route::delete('/pets/{pet}/records/{record}', [HomeController::class, 'petRecordDestroy'])->name('pets.records.destroy');

    Route::get('/posts', [HomeController::class, 'posts'])->name('posts.index');
    Route::get('/posts/create', [HomeController::class, 'postCreate'])->name('posts.create');
    Route::post('/posts', [HomeController::class, 'postStore'])->name('posts.store');
    Route::get('/posts/{post}', [HomeController::class, 'postShow'])->name('posts.show');
    Route::get('/posts/{post}/edit', [HomeController::class, 'postEdit'])->name('posts.edit');
    Route::put('/posts/{post}', [HomeController::class, 'postUpdate'])->name('posts.update');
    Route::delete('/posts/{post}', [HomeController::class, 'postDestroy'])->name('posts.destroy');
    Route::post('/posts/{post}/comments', [HomeController::class, 'commentStore'])->name('posts.comments.store');
    Route::post('/posts/{post}/like', [HomeController::class, 'postLike'])->name('posts.like');
    Route::post('/posts/{post}/comments/{comment}/like', [HomeController::class, 'commentLike'])->name('posts.comments.like');
    Route::get('/users/{user}', [HomeController::class, 'userShow'])->name('users.show');
});

Route::passkeys();
