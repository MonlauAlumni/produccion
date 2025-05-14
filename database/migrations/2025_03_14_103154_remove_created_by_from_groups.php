<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la columna
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
        });
    }
};
