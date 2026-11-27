<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('aula_configuracion')) {
            Schema::create('aula_configuracion', function (Blueprint $table) {
                $table->unsignedTinyInteger('id')->primary();
                $table->boolean('entregas_parciales')->default(true);
                $table->timestamps();
            });
        }
        DB::table('aula_configuracion')->insertOrIgnore(['id' => 1, 'entregas_parciales' => true, 'created_at' => now(), 'updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se conserva la configuración existente.
    }
};
