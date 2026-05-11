<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Event
 * 
 * Se encarga del registro de la telemetría y métricas de uso de la plataforma.
 * Almacena interacciones específicas (reproducciones, clics, redirecciones)
 * para su posterior análisis en el panel de administración.
 */
class Event extends Model
{
    /**
     * Atributos asignables mediante Mass Assignment.
     * 
     * 'event_type': Categoría de la interacción.
     * 'value': Valor métrico (ej: segundos escuchados).
     * 'metadata': Información adicional flexible.
     */
    protected $fillable = [
        'event_type',
        'value',
        'song_id',
        'metadata',
    ];

    /**
     * Conversión de tipos (Casting).
     * 
     * 'metadata' => 'array': Convierte automáticamente el formato JSON de la 
     * base de datos a un array de PHP al acceder al atributo, y viceversa.
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    /**
     * Relación Muchos a Uno (Inversa): Canción.
     * 
     * Vincula el evento con la pista musical correspondiente. 
     * Permite que el administrador sepa exactamente qué canción generó el evento.
     */
    public function song() {
        return $this->belongsTo(Song::class);
    }
}

