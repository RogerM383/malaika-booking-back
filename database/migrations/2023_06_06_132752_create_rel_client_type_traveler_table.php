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
        Schema::create('rel_client_type_traveler', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_type_id')->constrained('client_types');
            $table->foreignId('traveler_id')->constrained('travelers');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rel_client_type_traveler');
    }
};
