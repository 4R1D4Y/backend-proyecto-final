<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Song;

/**
 * Controlador de Canciones (SongController).
 * 
 * Gestiona la lógica de consulta y entrega del catálogo musical.
 * Implementa un sistema de filtrado dinámico y personalización de respuesta
 * según el estado de autenticación del usuario.
 */

class SongController extends Controller
{
    /**
     * Lista el catálogo completo con filtros y estados de interacción.
     * 
     * Utiliza 'withExists' para optimizar la carga de relaciones y reducir 
     * el número de consultas a la base de datos (problema N+1).
     */
    public function index(Request $request) {
        // Identificación opcional del usuario mediante el guard de Sanctum.
        // Permite saber si el usuario ha dado "Like" o "Favorito" incluso en rutas públicas.
        $user = auth('sanctum')->user();
        $userId = $user ? $user->id : null;

        // Cláusula de seguridad: Solo se muestran canciones con estado 'active'.
        $query = Song::where('status', 'active');

        /**
         * Optimización Eloquent:
         * Se añaden columnas booleanas virtuales (is_liked, is_favorite) verificando
         * la existencia de la relación en las tablas pivote para el usuario actual.
         */
        $query->withExists(['likedBy as is_liked' => function($q) use ($userId) {
            $q->where('user_id', $userId);
        }])->withExists(['favoritedBy as is_favorite' => function($q) use ($userId) {
            $q->where('user_id', $userId);
        }]);

        // 2. Sistema de Ordenación Dinámica basado en Query Params.
        $sortType = $request->query('sort');

        if ($sortType === 'recent') {
            $query->orderBy('release_date', 'desc');
        } elseif ($sortType === 'reproductions') {
            $query->orderBy('reproductions', 'desc');
        } elseif ($sortType === 'name_desc') {
            $query->orderBy('name', 'asc');
        } elseif ($sortType === 'oldest') {
            $query->orderBy('release_date', 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }

        /**
         * Transformación de Respuesta (Mapping):
         * Genera las URLs absolutas para los recursos multimedia (asset) y
         * garantiza que los tipos de datos sean consistentes (booleans, integers)
         * para su correcto procesamiento en el Frontend de React.
         */
        return $query->get()->map(function ($song) {
            return [
                'id' => $song->id,
                'name' => $song->name,
                'audio_path' => asset('storage/' . $song->audio_path),
                'cover_path' => asset('storage/' . $song->cover_path),
                'duration' => $song->duration,
                'reproductions' => $song->reproductions,
                'release_date' => $song->release_date,
                'type' => $song->type,
                'collection_name' => $song->collection_name,
                'collection_order' => $song->collection_order,
                'is_liked' => (bool)$song->is_liked,
                'is_favorite' => (bool)$song->is_favorite,
            ];
        });
    }

    /**
     * Muestra el detalle de una canción específica.
     * Implementa validación de estado para prevenir acceso a contenido oculto.
     */
    public function show(Song $song) {
        if ($song->status !== 'active') {
            return response()->json(['message' => 'Canción no disponible'], 404);
        }

        return response()->json([
            'id' => $song->id,
            'name' => $song->name,
            'audio_path' => asset('storage/' . $song->audio_path),
            'cover_path' => asset('storage/' . $song->cover_path),
            'duration' => $song->duration,
            'reproductions' => $song->reproductions,
            'release_date' => $song->release_date,
            'type' => $song->type,
            'collection_name' => $song->collection_name,
            'collection_order' => $song->collection_order
        ]);
    }
}

