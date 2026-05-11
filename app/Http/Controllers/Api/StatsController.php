<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Song;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Estadísticas y Telemetría (StatsController).
 * 
 * Este controlador gestiona la persistencia de interacciones del usuario 
 * capturadas desde el Frontend, permitiendo auditar el comportamiento del 
 * usuario y el rendimiento del catálogo musical.
 */

class StatsController extends Controller
{
    /**
     * Almacena un evento de telemetría en la base de datos.
     * 
     * Implementa una validación estricta para asegurar la integridad de los 
     * datos analíticos y actualiza dinámicamente los contadores de canciones.
     */
    public function storeEvent(Request $request) 
    {
        // Validación de la estructura del evento según el esquema de la base de datos
        $validated = $request->validate([
            'event_type' => 'required|in:playtime,license_view,catalog_click,social_redirect',
            'song_id' => 'nullable|exists:songs,id', // Verifica que la canción exista
            'value' => 'nullable|integer',
            'metadata' => 'nullable|array'
        ]);

        // Creación del registro histórico en la tabla 'events'
        Event::create([
            'event_type' => $validated['event_type'],
            'song_id' => $validated['song_id'] ?? null,
            'value' => $validated['value'] ?? 0,
            'metadata' => $validated['metadata'] ?? null,
        ]);

        /**
         * Lógica de Negocio: Incremento de Reproducciones.
         * 
         * Se implementa un filtro de seguridad: solo sumamos una reproducción real 
         * cuando se recibe un evento de tipo 'playtime' con valor 0. 
         * Esto identifica el "inicio" de la escucha desde el cliente, diferenciándolo
         * de los eventos de pausa o tracking de tiempo parcial (donde value > 0).
         */
        if ($validated['event_type'] === 'playtime' && $validated['value'] === 0 && isset($validated['song_id'])) {
            // Operación atómica en la base de datos para evitar colisiones
            Song::where('id', $validated['song_id'])->increment('reproductions');
        }

        // Respuesta estandarizada JSON para el cliente (API REST)
        return response()->json(['message' => 'Evento registrado con éxito'], 201);
    }
}

