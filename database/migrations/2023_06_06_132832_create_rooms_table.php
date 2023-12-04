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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();

            $table->foreignId('room_type_id')->constrained('room_types');
            $table->foreignId('departure_id')->constrained('departures');

            $table->unsignedTinyInteger('room_number');

            $table->text('observations')->nullable(true);

            $table->timestamps();
            $table->softDeletes();

            // Definimos restricción para que no puede haber numeros de habitacion repetidos por salida.
            //$table->unique(['departure_id', 'room_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
