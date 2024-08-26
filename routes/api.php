<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\TourneysController;
use Illuminate\Support\Facades\Auth;

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

Route::get('/data', [ExampleController::class, 'getData']);

// Rota para criação de temas
Route::post('/themes', [ThemeController::class, 'store']);

// Rota para listar todos os temas
Route::get('/themes', [ThemeController::class, 'index']);

// Rota para deletar um tema por ID
Route::delete('/themes/{id}', [ThemeController::class, 'destroy']);

Route::post('login', [AuthenticatedSessionController::class, 'store']);
// Exibir formulário de registro
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');

// Processar registro
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::middleware('auth:sanctum')->get('/check-token', function (Request $request) {
    return response()->json([
        'user_creator_id' => $request->user()->id,
        'user_name' => $request->user()->name,
    ]);
});

Route::get('/tourneys', [TourneysController::class, 'index']);
Route::post('/tourneys', [TourneysController::class, 'store']);
Route::get('/tourneys/{id}', [TourneysController::class, 'show']);