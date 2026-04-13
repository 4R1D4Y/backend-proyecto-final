<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SongController;
use App\Http\Controllers\Api\InteractionController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\AdminController;


/*

|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// --- PÚBLICO ---
Route::get('/songs', [SongController::class, 'index']);
Route::get('/songs/{song}', [SongController::class, 'show']); // Para ver el detalle de una canción
Route::post('/events', [StatsController::class, 'storeEvent']); // Registro de clics/reproducciones (público)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// --- SOLO USUARIOS LOGUEADOS (Cualquier Rol) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Perfil del usuario actual (muy útil para React)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Página de Favoritos
    Route::get('/user/favorites', [InteractionController::class, 'getUserFavorites']);

    // Ajustes de Cuenta
    Route::put('/user/email', [AuthController::class, 'updateEmail']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
    Route::delete('/user/delete', [AuthController::class, 'deleteAccount']);
    
    // Likes y Favoritos (InteractionController)
    Route::post('/songs/{song}/favorite', [InteractionController::class, 'toggleFavorite']);
    Route::post('/songs/{song}/like', [InteractionController::class, 'toggleLike']);
});

// --- SOLO ADMIN ---
Route::middleware(['auth:sanctum', 'can:admin-only'])->prefix('admin')->group(function () {
    // Estadísticas globales corregidas a EventController
    Route::get('/stats', [StatsController::class, 'getGlobalStats']);

    // Gestión de canciones
    Route::get('/songs/all', [AdminController::class, 'listAllSongs']);
    Route::post('/songs', [AdminController::class, 'storeSong']);
    Route::put('/songs/{song}', [AdminController::class, 'updateSong']);
    Route::patch('/songs/{song}/status', [AdminController::class, 'toggleSongStatus']);
    Route::delete('/songs/{song}', [AdminController::class, 'destroySong']); // Por si se necesita borrar físicamente

    // Gestión de usuarios
    Route::get('/users', [AdminController::class, 'listUsers']);
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus']);
});