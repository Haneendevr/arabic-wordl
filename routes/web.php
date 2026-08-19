<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordlController;

// عرض الصفحة الرئيسية والبدء
Route::get('/', [WordlController::class, 'index']);
Route::get('/new-game', [WordlController::class, 'startNewGame']);

// تقييد التخمين بـ 30 محاولة فقط في الدقيقة لكل مستخدم لحماية السيرفر
Route::middleware('throttle:30,1')->group(function () {
    Route::post('/guess', [WordlController::class, 'makeGuess']);
});