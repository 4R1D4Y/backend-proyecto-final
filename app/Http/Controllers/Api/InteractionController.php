<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Song;

class InteractionController extends Controller
{
    public function getUserFavorites(Request $request) {
        $favorites = $request->user()->favorites()
            ->where('status', 'active') // Solo mostramos las que no están ocultas
            ->get()
            ->map(function ($song) {
                return [
                    'id' => $song->id,
                    'name' => $song->name,
                    'audio' => asset('storage/' . $song->audio_path),
                    'cover' => asset('storage/' . $song->cover_path),
                    'duration' => $song->duration,
                    'saved_date' => $song->pivot->saved_date, // Fecha en que se guardó
                ];
            });

        return response()->json($favorites);
    }

    public function toggleFavorite(Request $request, Song $song) {
        if ($request->user()->status !== 'active') {
            return response()->json(['message' => 'Usuario no autorizado por suspensión o bloqueo.'], 403);
        }

        $request->user()->favorites()->toggle($song->id, [
            'saved_date' => now()
        ]);

        $isFavorite = $request->user()->favorites()->where('song_id', $song->id)->exists();

        return response()->json([
            'status' => 'success',
            'is_favorite' => $isFavorite
        ]);
    }

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
