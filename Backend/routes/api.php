<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommitteeController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserProgramController;
use App\Http\Controllers\Api\UserCommitteeController;


Route::get('/test', function () {
     return response()->json([
          'message' => 'API working'
     ]);
});
Route::get('/login', function () {
    return response()->json(['message' => 'Unauthorized'], 401);
})->name('login');
Route::post('/admin-login', [AuthController::class, 'login']);
//auth or admin routes 
Route::middleware('auth:sanctum')->group(function () {
     Route::post('/logout', [AuthController::class, 'logout']);
     Route::get('/me', [AuthController::class, 'me']);

     Route::apiResource('committees', CommitteeController::class)
          ->except(['index', 'show']);
     Route::apiResource('members', UserController::class)
          ->except(['index', 'show']);


     Route::apiResource('programs', ProgramController::class)
          ->except(['index', 'show']);


     Route::apiResource('pages', PageController::class)
          ->except(['index', 'show']);


     Route::apiResource('posts', PostController::class)
          ->except(['index', 'show']);


     Route::post('members/{member}/programs', [UserProgramController::class, 'attach']);
     Route::delete('members/{member}/programs/{program}', [UserProgramController::class, 'detach']);


     Route::post('members/{member}/committees', [UserCommitteeController::class, 'attach']);
     Route::patch('members/{member}/committees/{committee}', [UserCommitteeController::class, 'updateOrder']);
     Route::delete('members/{member}/committees/{committee}', [UserCommitteeController::class, 'detach']);
});


//guest page route 
Route::get('/committees', [CommitteeController::class, 'index']);
Route::get('committees/{committee}', [CommitteeController::class, 'show']);

Route::get('/members', [UserController::class, 'index']);
Route::get('members/{member}', [UserController::class, 'show']);


Route::get('programs', [ProgramController::class, 'index']);
Route::get('programs/{program}', [ProgramController::class, 'show']);

Route::get('pages', [PageController::class, 'index']);
Route::get('pages/{page}', [PageController::class, 'show']);

Route::get('posts', [PostController::class, 'index']);
Route::get('posts/{post}', [PostController::class, 'show']);