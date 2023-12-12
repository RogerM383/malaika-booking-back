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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Default MALAIKA
            $table->foreignId('client_type_id')->default(1)->constrained('client_types')->cascadeOnDelete();

            $table->string('dni')->nullable()->unique();
            $table->date('dni_expiration')->nullable();
            $table->string('place_birth')->nullable();
            $table->string('name')->nullable(false);
            $table->string('surname')->nullable(true);
            $table->string('email')->nullable(true);
            $table->string('phone')->nullable(true);
            $table->string('address')->nullable(true);

            $table->string('intolerances')->nullable(true);
            $table->string('frequent_flyer')->nullable(true);

            // Aixo es el numero del MANC -> no pot estar omplert i ser d'un tipo diferent a MNAC id 2
            $table->string('member_number')->nullable(true);
            $table->text('notes')->nullable(true);

            $table->foreignId('language_id')->default(1)->constrained('languages');

            $table->text('observations')->nullable(true)->default(null);
            $table->string('seat')->nullable(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
