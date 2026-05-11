<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Song;

/**
 * Controlador de Interacciones (InteractionController).
 * 
 * Gestiona la lógica de personalización del usuario, incluyendo el sistema de
 * "Me gusta" y la biblioteca de "Favoritos". Utiliza relaciones de Eloquent
 * para gestionar tablas pivote y asegurar la integridad de los datos.
 */

class InteractionController extends Controller
{
    /**
     * Recupera la lista de canciones marcadas como favoritas por el usuario.
     * 
     * Implementa un filtrado de seguridad para asegurar que, aunque una canción sea favorita,
     * solo se muestre si su estado actual en el catálogo es 'active'.
     */
    public function getUserFavorites(Request $request) {
        $user = $request->user();

        $favorites = $user->favorites()
            ->where('status', 'active')
            ->get()
            ->map(function ($song) use ($user) {
                // Transformación de la respuesta para el Frontend de React
                return [
                    'id' => $song->id,
                    'name' => $song->name,
                    'audio_path' => asset('storage/' . $song->audio_path),
                    'cover_path' => asset('storage/' . $song->cover_path),
                    'collection_name' => $song->collection_name,
                    'duration' => $song->duration,
                    // Acceso a datos adicionales almacenados en la tabla pivote (saved_date)
                    'saved_date' => $song->pivot->saved_date,
                    // Verificación cruzada: comprueba si la canción favorita también tiene un "Like"
                    'is_liked' => $user->likes()->where('song_id', $song->id)->exists(),
                ];
            });

        return response()->json($favorites);
    }

    /**
     * Alterna (Toggle) el estado de una canción en la lista de favoritos.
     * 
     * Utiliza el método 'toggle' de Eloquent, que añade el registro si no existe
     * o lo elimina si ya estaba presente, simplificando la lógica de la API.
     */
    public function toggleFavorite(Request $request, Song $song) {
        // Barrera de Seguridad: Usuarios suspendidos o bloqueados no pueden interactuar
        if ($request->user()->status !== 'active') {
            return response()->json(['message' => 'Usuario no autorizado por suspensión o bloqueo.'], 403);
        }

        // Registro del evento con marca de tiempo actual en la tabla pivote
        $request->user()->favorites()->toggle($song->id, [
            'saved_date' => now()
        ]);

        // Verificación del estado final para informar correctamente a la UI de React
        $isFavorite = $request->user()->favorites()->where('song_id', $song->id)->exists();

        return response()->json([
            'status' => 'success',
            'is_favorite' => $isFavorite
        ]);
    }

    /**
     * Alterna (Toggle) el "Me gusta" de una canción.
     * 
     * Sigue un patrón idéntico al de favoritos para mantener la consistencia en la API,
     * gestionando la relación en la tabla pivote 'likes'.
     */
    public function toggleLike(Request $request, Song $song) {
        if ($request->user()->status !== 'active') {
            return response()->json(['message' => 'Usuario no autorizado.'], 403);
        }

        $request->user()->likes()->toggle($song->id, [
            'liked_date' => now()
        ]);

        $isLiked = $request->user()->likes()->where('song_id', $song->id)->exists();

        return response()->json([
            'status' => 'success',
            'is_liked' => $isLiked
        ]);
    }
}
