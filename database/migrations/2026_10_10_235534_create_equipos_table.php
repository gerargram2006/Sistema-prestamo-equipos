<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Migración: tabla "equipos"
|--------------------------------------------------------------------------
| Crea la tabla del inventario:
|  - codigo: único (ej. LAP-001).
|  - nombre y categoria.
|  - estado: 'Disponible' o 'Prestado' (por defecto Disponible).
|  - estudiante_id: clave foránea al usuario que lo tiene (null = libre).
|
*/

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('categoria');
            $table->enum('estado', ['Disponible', 'Prestado'])->default('Disponible');
            $table->foreignId('estudiante_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
