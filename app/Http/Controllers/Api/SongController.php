<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Song;

class SongController extends Controller
{
    public function index(Request $request) {
        $user = auth('sanctum')->user();
        $userId = $user ? $user->id : null;

        $query = Song::where('status', 'active');

        // 1. Iniciamos la consulta con los checks de Like y Favorito
        $query->withExists(['likedBy as is_liked' => function($q) use ($userId) {
            $q->where('user_id', $userId);
        }])->withExists(['favoritedBy as is_favorite' => function($q) use ($userId) {
            $q->where('user_id', $userId);
        }]);

        // 2. Aplicamos filtros dinámicos
        $sortType = $request->query('sort');

        if ($sortType === 'recent') {
            $query->orderBy('release_date', 'desc');
        } elseif ($sortType === 'reproductions') {
            $query->orderBy('reproductions', 'desc');
        } elseif ($sortType === 'name_desc') { // Prueba esta para confirmar
            $query->orderBy('name', 'asc');
        } elseif ($sortType === 'oldest') {
            $query->orderBy('release_date', 'asc');
        } else {
            $query->orderBy('id', 'asc');
        }

        // 3. Obtenemos y formateamos la respuesta
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
                // Importante: pasar estos booleanos al frontend
                'is_liked' => (bool)$song->is_liked,
                'is_favorite' => (bool)$song->is_favorite,
            ];
        });
    }

    public function show(Song $song) {
        // Verificamos que no esté oculta para el público
        if ($song->status !== 'active') {
            return response()->json(['message' => 'Canción no disponible'], 404);
        }

        // Formateamos las rutas para React
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
