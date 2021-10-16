<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/auth/login', [AuthController::class, 'getToken']);
Route::post('/users', [UserController::class, 'store']);

Route::middleware(['auth:sanctum'])->group(function() {
  Route::get('/auth/logout', [AuthController::class, 'deleteToken']);

  Route::get('/users', [UserController::class, 'index']);
  Route::get('/users/{id}', [UserController::class, 'show']);
  Route::put('/users/{id}', [UserController::class, 'update']);
  Route::delete('/users/{id}', [UserController::class, 'destroy']);
});
