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
        if (! Schema::hasColumn('aula_devoluciones', 'errores')) {
            Schema::table('aula_devoluciones', fn (Blueprint $table) => $table->text('errores')->nullable());
        }
        if (! Schema::hasColumn('aula_devoluciones', 'calificacion')) {
            Schema::table('aula_devoluciones', fn (Blueprint $table) => $table->string('calificacion', 100)->nullable());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se conservan las devoluciones y calificaciones existentes.
    }
};
