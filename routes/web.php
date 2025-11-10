<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-gemini', [\App\Http\Controllers\BotController::class, 'testGemini']);

