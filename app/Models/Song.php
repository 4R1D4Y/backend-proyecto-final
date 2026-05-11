<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo Song
 * 
 * Gestiona la información de las pistas musicales y sus metadatos asociados.
 * Sirve como eje central para las estadísticas de reproducción y las 
 * interacciones de los usuarios.
 */
class Song extends Model
{
    /**
     * Atributos asignables (Mass Assignment).
     * 
     * Incluye metadatos técnicos (duración, paths de archivos) y 
     * organizativos (tipo de lanzamiento, colecciones).
     */
    protected $fillable = [
        'name',
        'audio_path',
        'duration',
        'release_date',
        'cover_path',
        'type', // Single, EP o Álbum
        'collection_name',
        'collection_order',
        'reproductions', // Contador acumulativo de escuchas
        'status', // Control de visibilidad: 'active' o 'hidden'
    ];

    /**
     * Relación Uno a Muchos: Eventos.
     * 
     * Vincula la canción con sus registros de telemetría (logs de escucha, clics).
     * Permite realizar análisis detallados sobre el rendimiento de cada pista.
     */
    public function events() {
        return $this->hasMany(Event::class);
    }

    /**
     * Relación Muchos a Muchos (Inversa): Favoritos.
     * 
     * Permite acceder a la lista de usuarios que han marcado esta canción como favorita.
     * Utiliza la tabla pivote 'favorites'.
     */
    public function favoritedBy() {
        return $this->belongsToMany(User::class, 'favorites');
    }

    /**
     * Relación Muchos a Muchos (Inversa): Me gusta (Likes).
     * 
     * Facilita la obtención de estadísticas sobre la aceptación de la pista
     * entre la comunidad de usuarios.
     */
    public function likedBy() {
        return $this->belongsToMany(User::class, 'likes');
    }
}

