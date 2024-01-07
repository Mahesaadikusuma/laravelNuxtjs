<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\TeamController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\ResponbilityController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// COMPANY API
Route::prefix('company')
     ->middleware('auth:sanctum')
     ->name('company.')
     ->group(function () {
     Route::get('', [CompanyController::class, 'fetch'])->name('fetch');
     Route::POST('', [CompanyController::class, 'create'])->name('create');
     Route::post('update/{id}', [CompanyController::class, 'update'])->name('update');
});


// Route::get('company', [CompanyController::class, 'all']);
// Route::POST('company', [CompanyController::class, 'create'])->middleware('auth:sanctum');
// Route::put('company', [CompanyController::class, 'update'])->middleware('auth:sanctum');


// Team API
Route::prefix('team')
     ->middleware('auth:sanctum')
     ->name('team.')
     ->group(function () {
     Route::get('', [TeamController::class, 'fetch'])->name('fetch');
     Route::POST('', [TeamController::class, 'create'])->name('create');
     Route::post('/update/{id}', [TeamController::class, 'update'])->name('update');
     Route::delete('{id}', [TeamController::class, 'destroy'])->name('destroy');
});

// Role API
Route::prefix('role')
     ->middleware('auth:sanctum')
     ->name('role.')
     ->group(function () {
     Route::get('', [RoleController::class, 'fetch'])->name('fetch');
     Route::post('', [RoleController::class, 'create'])->name('create');
     Route::post('/update/{id}', [RoleController::class, 'update'])->name('update');
     Route::delete('{id}', [RoleController::class, 'destroy'])->name('destroy');
});

// Route Responbility
Route::prefix('responbility')
     ->middleware('auth:sanctum')
     ->name('responbility.')
     ->group(function () {
     Route::get('', [ResponbilityController::class, 'fetch'])->name('fetch');
     Route::post('', [ResponbilityController::class, 'create'])->name('create');
     
     Route::delete('{id}', [ResponbilityController::class, 'destroy'])->name('destroy');
});


// ROUTE EMPLOYEE
Route::prefix('employee')
     ->middleware('auth:sanctum')
     ->name('employee.')
     ->group(function () {
     Route::get('', [EmployeeController::class, 'fetch'])->name('fetch');
     Route::POST('', [EmployeeController::class, 'create'])->name('create');
     Route::post('/update/{id}', [EmployeeController::class, 'update'])->name('update');
     Route::delete('{id}', [EmployeeController::class, 'destroy'])->name('destroy');
});


// AUTH API
Route::name('auth.')->group(function() {
     Route::POST('login', [UserController::class, 'login']);
     Route::POST('register', [UserController::class, 'register']);

     Route::middleware('auth:sanctum')->group( function() {
          Route::POST('logout', [UserController::class, 'logout'])->name('logout');
          Route::get('user', [UserController::class, 'fetch'])->name('fetch');   
     });
});

