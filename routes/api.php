<?php

use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// The Telegram webhook route. It's a POST request and should be outside of standard CSRF protection,
// which is handled in the VerifyCsrfToken middleware exception list.
Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);

// Standard API route example (optional, but good for a base project)
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

