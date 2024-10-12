<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;

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


// Permission Routes
Route::apiResource('permissions', PermissionController::class)->middleware('permission:full_access');


// Role Routes
Route::group(['middleware' => ['permission:full_access']], function () {
    Route::apiResource('roles', RoleController::class);
    Route::post('roles/{roleId}/permissions', [RoleController::class, 'assignPermissions']);
});


// User Routes
Route::apiResource('users', PermissionController::class)->middleware('permission:full_access');
