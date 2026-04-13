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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('audio_path')->unique();
            $table->integer('duration')->nullable();
            $table->date('release_date');
            $table->string('cover_path');
            $table->enum('type', ['single', 'ep', 'album'])->default('single');
            $table->string('collection_name')->nullable();
            $table->integer('collection_order')->nullable();
            $table->integer('reproductions')->default(0);
            $table->enum('status', ['active', 'hidden'])->default('active');
            $table->timestamps();

            $table->unique(['collection_name', 'collection_order']);
        });

        DB::statement(
            "ALTER TABLE songs ADD CONSTRAINT check_collection_fields
            CHECK ((type = 'single') OR (type IN ('ep', 'album') AND collection_order IS NOT NULL))"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
