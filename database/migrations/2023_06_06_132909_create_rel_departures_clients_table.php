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
        Schema::create('rel_departures_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departure_id')->constrained('departures');
            $table->foreignId('client_id')->constrained('clients');

            // TODO: Mirar cpomo manejar esto, hay que crear una Pivot model?
            $table->foreignId('room_type_id')->constrained('room_types');

            $table->tinyInteger('state')->default(0)->nullable(false);
            $table->tinyInteger('number_room')->nullable(true);
            //$table->integer('type_room')->default(2)->nullable(false);
            $table->text('observations')->nullable(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rel_departures_clients');
    }
};
