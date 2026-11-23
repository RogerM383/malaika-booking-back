<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * trips.closed tenía el sentido invertido (1 = abierto). Se invierten también los eliminados
 * para que conserven su estado si se restauran. Desplegar a la vez que el front.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('trips')->update(['closed' => DB::raw('1 - closed')]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE trips ALTER COLUMN closed SET DEFAULT 1');
        }
    }

    public function down(): void
    {
        DB::table('trips')->update(['closed' => DB::raw('1 - closed')]);

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE trips ALTER COLUMN closed SET DEFAULT 0');
        }
    }
};
