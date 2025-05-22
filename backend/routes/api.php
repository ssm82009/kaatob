<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PoemController;
use App\Http\Controllers\SettingController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Poem routes
Route::get('/poems', [PoemController::class, 'index']);
Route::get('/poems/{id}', [PoemController::class, 'show']);
Route::post('/poems', [PoemController::class, 'store']);
Route::put('/poems/{id}', [PoemController::class, 'update']);
Route::delete('/poems/{id}', [PoemController::class, 'destroy']);

// Generate poem using AI
Route::post('/generate-poem', [PoemController::class, 'generatePoem']);

// Settings routes
Route::get('/settings/ai', [SettingController::class, 'getAiSettings']);
Route::post('/settings/ai', [SettingController::class, 'updateAiSettings']);
