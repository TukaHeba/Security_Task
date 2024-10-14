<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
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
Route::group(['middleware' => ['permission:full_access']], function () {
    Route::apiResource('permissions', PermissionController::class);
    Route::get('/permissions/deleted', [PermissionController::class, 'listDeletedPermissions']);
    Route::post('/permissions/{id}/restore', [PermissionController::class, 'restorePermission']);
    Route::delete('/permissions/{id}/force-delete', [PermissionController::class, 'forceDeletePermission']);
});


// Role Routes
Route::group(['middleware' => ['permission:full_access']], function () {
    Route::apiResource('roles', RoleController::class);
    Route::get('/roles/deleted', [RoleController::class, 'listDeletedRoles']);
    Route::post('/roles/{id}/restore', [RoleController::class, 'restoreRole']);
    Route::delete('/roles/{id}/force-delete', [RoleController::class, 'forceDeleteRole']);
    Route::post('roles/{roleId}/permissions', [RoleController::class, 'assignPermissions']);
});


// User Routes
Route::group(['middleware' => ['permission:full_access']], function () {
    Route::apiResource('users', UserController::class);
    Route::get('/users/deleted', [UserController::class, 'listDeletedUsers']);
    Route::post('/users/{id}/restore', [UserController::class, 'restoreUser']);
    Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDeleteUser']);
});

// Task Routes
Route::apiResource('tasks', PermissionController::class);
