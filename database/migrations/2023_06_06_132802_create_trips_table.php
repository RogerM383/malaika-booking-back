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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->default(null)->nullable();
            $table->text('description')->nullable(true);
            //$table->string('category')->nullable(true);
            $table->text('commentary')->nullable(true);

            $table->foreignId('trip_state_id')->default(1)->constrained('trip_states');

            $table->string('image')->nullable()->default(null);
            $table->string('pdf')->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
