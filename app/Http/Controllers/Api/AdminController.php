<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // --- GESTIÓN DE CANCIONES ---
    public function listAllSongs() {
        return Song::orderBy('created_at', 'desc')->get()->map(function ($song) {
            $song->audio_url = asset('storage/' . $song->audio_path);
            $song->cover_url = asset('storage/' . $song->cover_path);
            return $song;
        });
    }

    public function storeSong(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'audio_path' => 'required|file|mimes:mp3,wav,m4a,mp4,aac|max:20000',
            'cover_path' => 'required|image|max:5000',
            'type' => 'required|in:single,ep,album',
            'release_date' => 'required|date',
            'duration' => 'required|integer',
            'collection_name' => 'nullable|string',
            'collection_order' => 'nullable|integer',
        ]);

        $audioPath = $request->file('audio_path')->store('songs', 'public');
        $coverPath = $request->file('cover_path')->store('covers', 'public');

        $validated['audio_path'] = $audioPath;
        $validated['cover_path'] = $coverPath;
        $validated['status'] = 'active';

        $song = Song::create($validated);

        return response()->json($song, 201);
    }

    public function updateSong(Request $request, Song $song) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:single,ep,album',
            'release_date' => 'required|date',
            'duration' => 'required|integer',
            'audio_path' => 'nullable|file|mimes:mp3,wav,m4a,mp4,aac',
            'cover_path' => 'nullable|image',
            'collection_name' => 'nullable|string',
            'collection_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('audio_path')) {
            Storage::disk('public')->delete($song->audio_path);
            $validated['audio_path'] = $request->file('audio_path')->store('songs', 'public');
        }

        if ($request->hasFile('cover_path')) {
            Storage::disk('public')->delete($song->cover_path);
            $validated['cover_path'] = $request->file('cover_path')->store('covers', 'public');
        }

        $song->update($validated);
        return response()->json($song);
    }

    public function toggleSongStatus(Request $request, Song $song) 
    {
        $request->validate(['status' => 'required|in:active,hidden']);
        
        $song->update(['status' => $request->status]);

        return response()->json([
            'message' => "La canción ahora está en estado: {$request->status}",
            'song' => $song
        ]);
    }

    public function destroySong(Song $song) {
        Storage::disk('public')->delete([$song->audio_path, $song->cover_path]);
        $song->delete();
        return response()->json(['message' => 'Canción eliminada permanentemente']);
    }

    
    // --- GESTIÓN DE USUARIOS ---
    public function listUsers() {
        return User::orderBy('created_at', 'desc')->get();
    }

    public function updateUserStatus(Request $request, User $user) {
        $request->validate([
            'status' => 'required|in:active,suspended,blocked',
            'suspension_time' => 'nullable|date|after:now'
        ]);

        $user->update([
            'status' => $request->status,
            'suspension_time' => $request->status === 'suspended' ? $request->suspension_time : null
        ]);

        return response()->json(['message' => 'Estado del usuario actualizado', 'user' => $user]);
    }    


    // ESTADISTICAS
    public function getDashboardStats() 
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
            'events_summary' => Event::select('event_type', DB::raw('count(*) as total'))
                                    ->groupBy('event_type')
                                    ->get()
        ]);
    }

    public function listEvents() 
    {
        // Esta es la función que te faltaba para la tabla de eventos
        return Event::with('song:id,name')
                    ->orderBy('created_at', 'desc')
                    ->limit(100)
                    ->get();
    }
}
