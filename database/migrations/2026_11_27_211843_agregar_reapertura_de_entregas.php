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
        if (! Schema::hasColumn('aula_tareas', 'permite_nueva_entrega')) {
            Schema::table('aula_tareas', fn (Blueprint $table) => $table->boolean('permite_nueva_entrega')->default(false));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se conserva el estado de las tareas existentes.
    }
};
