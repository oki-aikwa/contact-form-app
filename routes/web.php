<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- お問い合わせユーザー用（公開ルート） ---
Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contacts.confirm');
Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
Route::get('/thanks', [ContactController::class, 'thanks'])->name('contacts.thanks');

Route::resource('categories', CategoryController::class);

Route::get('/', function () {
    return redirect()->route('contacts.index');
});

// --- ゲスト（未ログイン）専用ルート ---
Route::middleware(['guest'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// --- 認証済み管理者専用ルート ---
Route::middleware(['auth'])->group(function () {

    // ログアウト処理
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 管理画面（お問い合わせ一覧・検索）
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // お問い合わせ詳細・削除
    Route::get('/admin/contacts/{contact}', [AdminController::class, 'show'])->name('admin.show');
    Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy'])->name('admin.contacts.destroy');

    // CSVエクスポート
    Route::get('/contacts/export', [AdminController::class, 'export'])->name('contacts.export');

    // タグ管理機能（CRUD）
    Route::post('/admin/tags', [TagController::class, 'store'])->name('tags.store');
    Route::get('/admin/tags/{tag}/edit', [TagController::class, 'edit'])->name('tags.edit');
    Route::put('/admin/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/admin/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

});

