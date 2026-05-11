<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * Controlador Administrativo (AdminController).
 * 
 * Centraliza las funciones de gestión del sistema, permitiendo el control 
 * total sobre el catálogo musical, la moderación de usuarios y la 
 * monitorización de métricas de rendimiento del aplicativo.
 */

class AdminController extends Controller
{
    // --- SECCIÓN: GESTIÓN DE CATÁLOGO (CRUD) ---

    /**
     * Lista todas las canciones registradas.
     * Transforma las rutas locales en URLs públicas accesibles para el Frontend.
     */
    public function listAllSongs() {
        return Song::orderBy('created_at', 'desc')->get()->map(function ($song) {
            $song->audio_url = asset('storage/' . $song->audio_path);
            $song->cover_url = asset('storage/' . $song->cover_path);
            return $song;
        });
    }

    /**
     * Almacena una nueva canción en el sistema.
     * Implementa la lógica de persistencia de archivos físicos en el disco 'public'
     * y registra los metadatos validados en la base de datos.
     */
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

        // Almacenamiento físico de archivos multimedia
        $audioPath = $request->file('audio_path')->store('songs', 'public');
        $coverPath = $request->file('cover_path')->store('covers', 'public');

        $validated['audio_path'] = $audioPath;
        $validated['cover_path'] = $coverPath;
        $validated['status'] = 'active';

        $song = Song::create($validated);
        return response()->json($song, 201);
    }

    /**
     * Actualiza los datos de una canción existente.
     * Incluye lógica de limpieza: si se suben nuevos archivos, se eliminan los anteriores
     * del servidor para evitar el consumo innecesario de almacenamiento.
     */
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
            Storage::disk('public')->delete($song->audio_path); // Eliminación del archivo antiguo
            $validated['audio_path'] = $request->file('audio_path')->store('songs', 'public');
        }

        if ($request->hasFile('cover_path')) {
            Storage::disk('public')->delete($song->cover_path);
            $validated['cover_path'] = $request->file('cover_path')->store('covers', 'public');
        }

        $song->update($validated);
        return response()->json($song);
    }

    /**
     * Alterna la visibilidad pública de una canción.
     */
    public function toggleSongStatus(Request $request, Song $song) 
    {
        $request->validate(['status' => 'required|in:active,hidden']);
        $song->update(['status' => $request->status]);
        return response()->json(['song' => $song]);
    }

    /**
     * Borrado físico y lógico.
     * Elimina el registro de la base de datos y los archivos asociados del servidor.
     */
    public function destroySong(Song $song) {
        Storage::disk('public')->delete([$song->audio_path, $song->cover_path]);
        $song->delete();
        return response()->json(['message' => 'Canción eliminada permanentemente']);
    }

    
    // --- SECCIÓN: MODERACIÓN DE USUARIOS ---

    public function listUsers() {
        return User::orderBy('created_at', 'desc')->get();
    }

    /**
     * Gestión de sanciones.
     * Permite activar, suspender o bloquear usuarios, gestionando el tiempo
     * de expiración para las suspensiones temporales.
     */
    public function updateUserStatus(Request $request, User $user) {
        $request->validate([
            'status' => 'required|in:active,suspended,blocked',
            'suspension_time' => 'nullable|date|after:now'
        ]);

        $user->update([
            'status' => $request->status,
            'suspension_time' => $request->status === 'suspended' ? $request->suspension_time : null
        ]);

        return response()->json(['user' => $user]);
    }    


    // --- SECCIÓN: ANALÍTICA Y BUSINESS INTELLIGENCE ---

    /**
     * Genera un resumen de métricas clave (KPIs).
     * Realiza cálculos agregados (sumas y conteos) sobre usuarios, canciones y eventos
     * para alimentar el Dashboard de estadísticas del administrador.
     */
    public function getDashboardStats() 
    {
        return response()->json([
            'overview' => [
                'total_users' => User::count(),
                'total_songs' => Song::count(),
                'total_reproductions' => Song::sum('reproductions'),
                'total_listen_time' => Event::where('event_type', 'playtime')->sum('value'),
            ],
            'top_songs' => Song::orderBy('reproductions', 'desc')
                            ->take(5)
                            ->get(['id', 'name', 'reproductions']),
            'events_summary' => Event::select('event_type', DB::raw('count(*) as total'))
                                    ->groupBy('event_type')
                                    ->get()
        ]);
    }

    /**
     * Recupera el historial de eventos recientes.
     * Utiliza Eager Loading ('with') para traer los nombres de las canciones asociadas
     * de forma eficiente (Query Optimization).
     */
    public function listEvents() 
    {
        return Event::with('song:id,name')
                    ->orderBy('created_at', 'desc')
                    ->limit(100)
                    ->get();
    }
}
