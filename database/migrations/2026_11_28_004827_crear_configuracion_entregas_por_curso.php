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
        if (! Schema::hasTable('aula_configuracion_cursos')) {
            Schema::create('aula_configuracion_cursos', function (Blueprint $table) {
                $table->string('curso', 40)->primary();
                $table->boolean('entregas_parciales')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se conserva la configuración guardada de los cursos.
    }
};
