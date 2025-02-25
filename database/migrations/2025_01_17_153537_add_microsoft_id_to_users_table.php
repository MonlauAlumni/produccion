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
        Schema::table('users', function (Blueprint $table) {
            // Añadir el campo 'microsoft_id' como cadena de texto
            $table->string('microsoft_id')->nullable()->unique();  // Asegúrate de que el ID sea único
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar el campo 'microsoft_id'
            $table->dropColumn('microsoft_id');
        });
    }
};
