<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\LinkedInConnectController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers')->group(function () {
    Auth::routes();
});

Route::redirect('/', 'todo');
Route::middleware('auth')->group(function () {
    Route::resource('/todo', TodoController::class);
    Route::get('/connect/linkedIn', [LinkedInConnectController::class,'connect'])->name('connect.LinkedIn');
    Route::get('/connect/linkedIn/callback', [LinkedInConnectController::class,'callback'])->name('connect.LinkedIn.callback');
});
