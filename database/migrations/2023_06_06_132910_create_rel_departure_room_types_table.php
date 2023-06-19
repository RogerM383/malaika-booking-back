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
        Schema::create('rel_departure_room_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departure_id')->constrained('departures');
            $table->foreignId('room_type_id')->constrained('room_types');
            $table->unsignedTinyInteger('quantity')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rel_departure_room_type');
    }
};
