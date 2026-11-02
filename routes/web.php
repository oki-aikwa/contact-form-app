<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// お問い合わせフォームの一連のルート
Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contacts.confirm');
Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
Route::get('/thanks', [ContactController::class, 'thanks'])->name('contacts.thanks');

// カテゴリとタグのリソースルート（リダイレクトや一覧用）
Route::resource('categories', CategoryController::class);
Route::resource('tags', TagController::class);

// トップページ（/）にアクセスした際もお問い合わせ画面にいくようにする場合は有効化してください
Route::get('/', function () {
    return redirect()->route('contacts.index');
});
// 管理者登録画面の一連のルート
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ログイン画面の一連のルート
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/admin', function () {
    return '管理画面（一覧表示エリア）';
})->middleware('auth');