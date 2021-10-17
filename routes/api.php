<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;

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

  Route::get('/customers', [CustomerController::class, 'index']);
  Route::post('/customers', [CustomerController::class, 'store']);
  Route::get('/customers/{id}', [CustomerController::class, 'show']);
  Route::put('/customers/{id}', [CustomerController::class, 'update']);

  // User admin routes
  Route::middleware(['user_is_admin'])->group(function() {
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy']);
    Route::put('/admin/users/{id}', [UserController::class, 'toggleAdminAccess']);

    Route::delete('/admin/customers/{id}', [CustomerController::class, 'destroy']);
  });
});
