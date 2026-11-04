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
Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contacts.confirm');
Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
Route::get('/thanks', [ContactController::class, 'thanks'])->name('contacts.thanks');

Route::resource('categories', CategoryController::class);

Route::get('/', function () {
    return redirect()->route('contacts.index');
});
Route::middleware(['guest'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/contacts/{contact}', [AdminController::class, 'show'])->name('admin.show');
    Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy'])->name('admin.contacts.destroy');
    Route::get('/contacts/export', [AdminController::class, 'export'])->name('contacts.export');
    Route::post('/admin/tags', [TagController::class, 'store'])->name('tags.store');
    Route::get('/admin/tags/{tag}/edit', [TagController::class, 'edit'])->name('tags.edit');
    Route::put('/admin/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/admin/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');
});

