<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResources([
    'user' => App\Http\Controllers\API\UserController::class,
]);

Route::get('profile', [App\Http\Controllers\API\UserController::class, 'profile']);
Route::put('profile', [App\Http\Controllers\API\UserController::class, 'updateProfile']);
Route::get('search', [App\Http\Controllers\API\UserController::class, 'searchUser']);


