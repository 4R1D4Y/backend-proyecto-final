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
            'created_at' => now()->toDateTimeString()
        ]);

        // Solo sumamos reproducción si el valor es EXACTAMENTE 0
        // (Cuando pausamos, enviamos 'elapsed' que siempre es >= 2, por lo que NO entrará aquí)
        if ($validated['event_type'] === 'playtime' && $validated['value'] === 0 && isset($validated['song_id'])) {
            Song::where('id', $validated['song_id'])->increment('reproductions');
        }

        return response()->json(['message' => 'Evento registrado con éxito'], 201);
    }
}
