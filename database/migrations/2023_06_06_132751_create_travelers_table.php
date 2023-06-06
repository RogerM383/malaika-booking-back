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
        Schema::create('travelers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            $table->string('seat')->nullable(true);
            $table->text('observations')->nullable(true);
            $table->string('intolerances')->nullable(true);
            $table->string('client_type')->nullable(true);
            $table->string('frequency_fly')->nullable(true);
            $table->string('type_room')->default(2)->nullable(true);
            $table->text('notes')->nullable(true);
            $table->string('member_number')->nullable(true);
            $table->string('lang')->nullable(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travelers');
    }
};
