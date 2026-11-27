<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('aula_plazos')) {
            Schema::create('aula_plazos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('docente_id')->constrained('users');
                $table->string('curso', 40);
                $table->string('materia', 100);
                $table->string('etapa', 180);
                $table->text('descripcion')->nullable();
                $table->dateTime('vence_en');
                $table->timestamps();
                $table->index(['curso', 'vence_en']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('aula_plazos');
    }
};
