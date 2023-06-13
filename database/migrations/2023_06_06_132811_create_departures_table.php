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

            $table->date('start')->nullable(true);
            $table->date('final')->nullable(true);
            $table->float('price', 8, 2)->nullable(true);
            $table->float('individual_supplement', 8, 2)->nullable(true);
            $table->string('pax_available')->nullable(true);
            $table->tinyInteger('state')->default('1')->nullable(false);
            $table->text('commentary')->nullable(true);
            $table->integer('expedient')->nullable(true);

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
