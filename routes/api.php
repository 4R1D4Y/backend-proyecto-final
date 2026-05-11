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



/**
 * Definición de Rutas de la API (api.php)
 * 
 * Este archivo orquestar los puntos de entrada (endpoints) del sistema,
 * aplicando capas de seguridad mediante Middlewares y organizando los 
 * recursos por niveles de privilegio.
 */

// --- SECCIÓN: ACCESO PÚBLICO ---
// Estas rutas no requieren token de autenticación para ser consultadas.
Route::get('/songs', [SongController::class, 'index']); // Catálogo general
Route::get('/songs/{song}', [SongController::class, 'show']); // Detalle de pista
Route::post('/events', [StatsController::class, 'storeEvent']); // Telemetría pública
Route::post('/login', [AuthController::class, 'login']); // Autenticación
Route::post('/register', [AuthController::class, 'register']); // Alta de usuario

// --- SECCIÓN: PRIVADA (Usuarios Autenticados) ---
// El middleware 'auth:sanctum' valida la vigencia del Token Bearer enviado desde React.
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Recuperación de perfil: Sincroniza el estado del usuario en el Frontend
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Gestión de la biblioteca personal
    Route::get('/user/favorites', [InteractionController::class, 'getUserFavorites']);

    // Endpoints de configuración de cuenta (Seguridad y Privacidad)
    Route::put('/user/email', [AuthController::class, 'updateEmail']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
    Route::delete('/user/delete', [AuthController::class, 'deleteAccount']); // Derecho al olvido
    
    // Interacciones sociales: Toggle de Likes y Favoritos
    Route::post('/songs/{song}/favorite', [InteractionController::class, 'toggleFavorite']);
    Route::post('/songs/{song}/like', [InteractionController::class, 'toggleLike']);
});

// --- SECCIÓN: RESTRINGIDA (Administración) ---
// Implementa doble capa de seguridad: Token válido + Verificación de Rol (Gate/Policy).
Route::middleware(['auth:sanctum', 'can:admin-only'])->prefix('admin')->group(function () {
    
    // Business Intelligence: Monitor de estadísticas y logs de eventos
    Route::get('/stats', [AdminController::class, 'getDashboardStats']);
    Route::get('/events', [AdminController::class, 'listEvents']);
    
    // Operaciones CRUD sobre el catálogo musical (solo administradores)
    Route::get('/songs/all', [AdminController::class, 'listAllSongs']);
    Route::post('/songs', [AdminController::class, 'storeSong']);
    Route::put('/songs/{song}', [AdminController::class, 'updateSong']);
    Route::patch('/songs/{song}/status', [AdminController::class, 'toggleSongStatus']);
    Route::delete('/songs/{song}', [AdminController::class, 'destroySong']);

    // Moderación y control de acceso de usuarios
    Route::get('/users', [AdminController::class, 'listUsers']);
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus']);
});
