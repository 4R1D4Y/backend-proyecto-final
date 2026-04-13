<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->enum('event_type', ['playtime', 'license_view', 'catalog_click', 'social_redirect']);
            $table->integer('value')->default(0);
            $table->foreignId('song_id')->nullable()->constrained('songs')->onDelete('cascade');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        DB::statement(
            "ALTER TABLE events ADD CONSTRAINT check_song_id_field CHECK (
            (event_type IN ('playtime', 'catalog_click') AND song_id IS NOT NULL)
            OR (event_type IN ('license_view', 'social_redirect') AND song_id IS NULL))"
        );
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
