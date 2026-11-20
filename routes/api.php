<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EtiquetaController;
use App\Http\Controllers\Api\TarefaController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/tarefas/estatisticas', [TarefaController::class, 'estatisticas']);
    Route::apiResource('tarefas', TarefaController::class);

    Route::get('/etiquetas', [EtiquetaController::class, 'index']);
    Route::delete('/etiquetas/{etiqueta}', [EtiquetaController::class, 'destroy']);
});
