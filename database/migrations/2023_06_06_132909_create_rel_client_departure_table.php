<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rel_client_departure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departure_id')->constrained('departures');
            $table->foreignId('client_id')->constrained('clients');

            $table->string('seat')->nullable(true);
            $table->unsignedTinyInteger('state')->default(1);
            $table->text('observations')->nullable(true);

            // Designa el tipo de habitacion solicitada.
            $table->foreignId('room_type_id')->constrained('room_types');

            $table->timestamps();

            // Agregar índice único compuesto para departure_id y client_id
            $table->unique(['departure_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rel_client_departure');
    }
};
