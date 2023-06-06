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
        Schema::create('departures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('trip_id')->constrained('trips')->cascadeOnDelete();

            $table->string('start')->nullable(true);
            $table->string('final')->nullable(true);
            $table->string('price')->nullable(true);
            $table->string('individual_supplement')->nullable(true);
            $table->string('pax_available')->nullable(true);
            $table->string('state')->default('1')->nullable(false);
            $table->text('commentary')->nullable(true);
            $table->integer('expedient')->nullable(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departures');
    }
};
