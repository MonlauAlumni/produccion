<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id(); // Clave primaria auto-incremental
            $table->foreignId('user_id') // Relación con la tabla 'users'
                  ->nullable() // Permitir valores nulos para relaciones opcionales
                  ->constrained('users') // Clave foránea hacia 'users.id'
                  ->nullOnDelete(); // Poner NULL si el usuario asociado es eliminado
            $table->string('name'); // Nombre de la empresa
            $table->string('phone', 15)->nullable(); // Teléfono de contacto, opcional
            $table->string('fiscal_id', 50)->unique(); // Identificación fiscal, única
            $table->text('description')->nullable(); // Descripción de la empresa, opcional
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
