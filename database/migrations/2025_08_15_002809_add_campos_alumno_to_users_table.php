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
    Schema::table('users', function (Blueprint $table) {
        // Campos adicionales para alumno
        $table->json('nombres')->nullable();       // ["Juan", "Carlos"]
        $table->json('apellidos')->nullable();     // ["Pérez", "Gómez"]
        $table->string('dni')->unique()->nullable();
        $table->enum('carrera', ['programacion'] )->default('programacion'); // Relacionar con tabla carreras
        $table->string('comision')->nullable();
        $table->date('fecha_nacimiento')->nullable();
        $table->string('link_git')->nullable();
        $table->string('link_linkedin')->nullable();
        $table->string('link_portfolio')->nullable();
        $table->string('foto_perfil')->nullable();
        $table->string('telefono')->nullable();
        $table->enum('rol', ['alumno', 'profesor'])->default('alumno');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'nombres',
            'apellidos',
            'dni',
            'carrera',
            'comision',
            'fecha_nacimiento',
            'link_git',
            'link_linkedin',
            'link_portfolio',
            'foto_perfil',
            'telefono',
            'rol'
        ]);
    });
}

};
