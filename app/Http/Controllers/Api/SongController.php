<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Song;

class SongController extends Controller
{
    public function index(Request $request) {
        $query = Song::where('status', 'active');

        // Filtros dinámicos desde React
        if ($request->sort === 'recent') {
            $query->orderBy('release_date', 'desc');
        } 
        elseif ($request->sort === 'reproductions') {
            $query->orderBy('reproductions', 'desc');
        }
        elseif ($request->sort === 'oldest') {
            $query->orderBy('release_date', 'asc');
        }

        return $query->get()->map(function ($song) {
            return [
                'id' => $song->id,
                'name' => $song->name,
                'audio_path' => asset('storage/' . $song->audio_path),
                'cover_path' => asset('storage/' . $song->cover_path),
                'duration' => $song->duration, // Útil para el reproductor en React
                'reproductions' => $song->reproductions,
                'release_date' => $song->release_date,
                'type' => $song->type,
                'collection_name' => $song->collection_name,
                'collection_order' => $song->collection_order
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
