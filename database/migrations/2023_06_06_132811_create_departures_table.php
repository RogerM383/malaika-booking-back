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
            $table->foreignId('state_id')->default(1)->constrained('departure_states');

            $table->date('start')->nullable(true);
            $table->date('final')->nullable(true);
            $table->float('price', 8, 2)->nullable(true);
            $table->float('taxes', 8, 2)->default(0)->nullable(true);
            $table->float('individual_supplement', 8, 2)->nullable(true);
            $table->float('booking_price', 8, 2)->default(0)->nullable(true);
            $table->string('pax_capacity')->nullable(true);
            $table->text('commentary')->nullable(true);
            $table->integer('expedient')->nullable(true);
            $table->boolean('hidden')->default(false);

            $table->timestamps();
            $table->softDeletes();
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
