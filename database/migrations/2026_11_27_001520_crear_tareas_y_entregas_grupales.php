<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('aula_integrantes_autorizados')) {
            Schema::create('aula_integrantes_autorizados', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users');
                $table->unsignedBigInteger('grupo_id');
                $table->foreignId('autorizado_por')->constrained('users');
                $table->timestamps();
                $table->unique(['user_id', 'grupo_id']);
            });
        }
        if (! Schema::hasTable('aula_tareas')) {
            Schema::create('aula_tareas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('grupo_id')->index();
                $table->foreignId('docente_id')->constrained('users');
                $table->string('titulo', 180);
                $table->string('materia', 100);
                $table->text('consigna');
                $table->dateTime('vence_en')->nullable();
                $table->timestamps();
            });
        }
        if (! Schema::hasTable('aula_entregas')) {
            Schema::create('aula_entregas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tarea_id')->constrained('aula_tareas');
                $table->foreignId('alumno_id')->constrained('users');
                $table->string('archivo');
                $table->string('nombre_original');
                $table->text('comentario')->nullable();
                $table->timestamps();
            });
        }
        if (! Schema::hasTable('aula_devoluciones')) {
            Schema::create('aula_devoluciones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('entrega_id')->constrained('aula_entregas');
                $table->foreignId('docente_id')->constrained('users');
                $table->text('comentario');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('aula_devoluciones');
        Schema::dropIfExists('aula_entregas');
        Schema::dropIfExists('aula_tareas');
        Schema::dropIfExists('aula_integrantes_autorizados');
    }
};
