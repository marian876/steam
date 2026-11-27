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
        if (! Schema::hasColumn('users', 'apellido')) {
            Schema::table('users', fn (Blueprint $table) => $table->string('apellido', 120)->default(''));
        }
        if (! Schema::hasColumn('users', 'activo')) {
            Schema::table('users', fn (Blueprint $table) => $table->boolean('activo')->default(true));
        }
        if (! Schema::hasColumn('users', 'curso')) {
            Schema::table('users', fn (Blueprint $table) => $table->string('curso', 40)->nullable());
        }
        if (! Schema::hasTable('grupos')) {
            Schema::create('grupos', function (Blueprint $table) {
                $table->id();
                $table->string('curso');
                $table->string('seccion');
                $table->string('especialidad');
                $table->unsignedInteger('numero');
                $table->unsignedInteger('anio');
                $table->boolean('activo')->default(true);
                $table->timestamps();
                $table->unique(['curso', 'seccion', 'especialidad', 'numero', 'anio']);
            });
        }
        if (! Schema::hasTable('grupo_user')) {
            Schema::create('grupo_user', function (Blueprint $table) {
                $table->foreignId('grupo_id')->constrained('grupos');
                $table->foreignId('user_id')->constrained('users');
                $table->timestamps();
                $table->primary(['grupo_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Se conservan tablas y columnas existentes; revertirlas podría borrar datos.
    }
};
