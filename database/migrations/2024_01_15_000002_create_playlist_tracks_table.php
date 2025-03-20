<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('playlist_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->constrained()->onDelete('cascade');
            $table->string('trackable_type');
            $table->unsignedBigInteger('trackable_id');
            $table->integer('position')->default(0);
            $table->timestamps();

            // Polimorphic index
            $table->index(['trackable_type', 'trackable_id']);

            // Ensure a track can only be added once to a playlist
            $table->unique(['playlist_id', 'trackable_type', 'trackable_id']);

            // Index for ordering
            $table->index(['playlist_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('playlist_tracks');
    }
};
