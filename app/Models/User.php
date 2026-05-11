<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo User
 * 
 * Representa a los usuarios de la plataforma y gestiona su autenticación, 
 * roles y permisos. Implementa el sistema de seguridad mediante tokens para la API.
 */

class User extends Authenticatable
{
    /**
     * HasApiTokens: Permite al usuario generar tokens de acceso mediante Laravel Sanctum.
     * Notifiable: Habilita el sistema de notificaciones integrado de Laravel.
     */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atributos asignables mediante Mass Assignment.
     * 
     * Incluye campos críticos para la moderación como 'role', 'status' 
     * y 'suspension_time'.
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'status',
        'suspension_time',
    ];

    /**
     * Atributos ocultos en las respuestas JSON de la API.
     * 
     * Por seguridad, se excluye el hash de la contraseña de cualquier respuesta 
     * para evitar la exposición de datos sensibles.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Conversión de tipos (Casting).
     * 
     * 'password' => 'hashed': Asegura que la contraseña se cifre automáticamente al guardarse.
     * 'suspension_time' => 'datetime': Convierte el string de la DB a un objeto Carbon (PHP).
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'suspension_time' => 'datetime',
        ];
    }

    /**
     * Relación Muchos a Muchos: Favoritos.
     * 
     * Define la conexión con el modelo Song a través de la tabla pivote 'favorites'.
     * 'withPivot' permite acceder a la fecha exacta en la que se guardó el registro.
     */
    public function favorites() {
        return $this->belongsToMany(Song::class, 'favorites')->withPivot('saved_date');
    }

    /**
     * Relación Muchos a Muchos: Me gusta (Likes).
     * 
     * Conecta al usuario con sus canciones puntuadas positivamente mediante 
     * la tabla pivote 'likes'.
     */
    public function likes() {
        return $this->belongsToMany(Song::class, 'likes')->withPivot('liked_date');
    }
}

