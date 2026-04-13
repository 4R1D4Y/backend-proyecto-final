<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Song;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function storeEvent(Request $request) 
    {
        $validated = $request->validate([
            'event_type' => 'required|in:playtime,license_view,catalog_click,social_redirect',
            'song_id' => 'nullable|exists:songs,id',
            'value' => 'nullable|integer',
            'metadata' => 'nullable|array'
        ]);

        Event::create([
            'event_type' => $validated['event_type'],
            'song_id' => $validated['song_id'] ?? null,
            'value' => $validated['value'] ?? 0,
            'metadata' => $validated['metadata'] ?? null,
            'created_at' => now()->toDateString()
        ]);

        // Si el evento es una reproducción, incrementamos el contador de la canción
        if ($validated['event_type'] === 'playtime' && isset($validated['song_id'])) {
            Song::where('id', $validated['song_id'])->increment('reproductions');
        }

        return response()->json(['message' => 'Evento registrado con éxito'], 201);
    }

    public function getGlobalStats() 
    {
        return response()->json([
            'overview' => [
                'total_users' => User::count(),
                'total_songs' => Song::count(),
                'total_reproductions' => Song::sum('reproductions'),
            ],
            'top_songs' => Song::orderBy('reproductions', 'desc')
                            ->take(5)
                            ->get(['id', 'name', 'reproductions']),
            'events_distribution' => Event::select('event_type', DB::raw('count(*) as total'))
                                    ->groupBy('event_type')
                                    ->get()
        ]);
    }
}
