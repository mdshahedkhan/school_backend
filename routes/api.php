<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\TeacherController;

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


Route::prefix('/v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'store'])->name('login');
    Route::post('/auth/register', [AuthController::class, 'create'])->name('register');

    Route::group(['prefix' => '/admin', 'middleware' => 'auth:api'], function () {
        Route::post('/logout', [AuthController::class, 'destroy']);
        Route::post('/teachers/status', [TeacherController::class, 'changeStatus']);
        Route::apiResources([
            'teachers' => TeacherController::class,
        ]);
    });
});
