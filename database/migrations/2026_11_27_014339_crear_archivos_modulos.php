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
        if (! Schema::hasTable('prompts_sitio_web')) {
            Schema::create('prompts_sitio_web', function (Blueprint $table) {
                $table->id();
                $table->string('clave')->unique();
                $table->string('titulo', 180);
                $table->text('descripcion');
                $table->longText('texto');
                $table->timestamps();
            });
        }
        if (! Schema::hasTable('archivos_modulos')) {
            Schema::create('archivos_modulos', function (Blueprint $table) {
                $table->id();
                $table->string('modulo', 60)->index();
                $table->string('titulo', 180);
                $table->string('archivo');
                $table->string('nombre_original');
                $table->boolean('activo')->default(true);
                $table->foreignId('user_id')->constrained('users');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se eliminan tablas con datos al revertir.
    }
};
