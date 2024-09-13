<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\TourneysController;
use App\Http\Controllers\EditionController;
use App\Http\Controllers\MatchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui é onde você pode registrar as rotas da API para sua aplicação.
| As rotas são carregadas pelo RouteServiceProvider dentro de um grupo
| que recebe o middleware "api".
|
*/

/**
 * Rotas de Autenticação
 */
Route::post('login', [AuthenticatedSessionController::class, 'store']);

// Exibir formulário de registro
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');

// Processar registro
Route::post('/register', [RegisteredUserController::class, 'store']);

// Verificar token autenticado
Route::middleware('auth:sanctum')->get('/check-token', function (Request $request) {
    return response()->json([
        'user_creator_id' => $request->user()->id,
        'user_name' => $request->user()->name,
    ]);
});

// Retorna o usuário autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Rotas de Temas
 */
Route::prefix('themes')->group(function () {
    // Rota para criação de temas
    Route::post('/', [ThemeController::class, 'store']);
    
    // Rota para listar todos os temas
    Route::get('/', [ThemeController::class, 'index']);
    
    // Rota para deletar um tema por ID
    Route::delete('/{id}', [ThemeController::class, 'destroy']);
});

/**
 * Rotas de Torneios (Tourneys)
 */
Route::prefix('tourneys')->group(function () {
    // Listar todos os torneios
    Route::get('/', [TourneysController::class, 'index']);
    
    // Criar um novo torneio
    Route::post('/', [TourneysController::class, 'store']);
    
    // Exibir um torneio específico por ID
    Route::get('/{id}', [TourneysController::class, 'show']);
    
    // Listar torneios por criador
    Route::get('/creator/{creatorId}', [TourneysController::class, 'getTourneysByCreator']);
    
    // Listar edições associadas a um torneio específico
    Route::get('/{tourney_id}/editions', [EditionController::class, 'getByTourneyId']);
});

/**
 * Rotas de Edições (Editions)
 */
Route::prefix('editions')->group(function () {
    // Criar uma nova edição
    Route::post('/', [EditionController::class, 'store']);
    
    // Deletar uma edição por ID
    Route::delete('/{id}', [EditionController::class, 'destroy']);
});

Route::post('/editions/{edition_id}/phases', [PhaseController::class, 'store']);


/**
 * Exemplo de Rota Simples
 */
Route::get('/data', [ExampleController::class, 'getData']);

Route::post('/tournaments/single-elimination', [MatchController::class, 'createSingleEliminationTournament']);



