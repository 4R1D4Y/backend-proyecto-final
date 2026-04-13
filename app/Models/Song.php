<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = [
        'name',
        'audio_path',
        'duration',
        'release_date',
        'cover_path',
        'type',
        'collection_name',
        'collection_order',
        'reproductions',
        'status',
    ];

    public function events() {
        return $this->hasMany(Event::class);
    }

    public function favoritedBy() {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function likedBy() {
        return $this->belongsToMany(User::class, 'likes');
    }
}
