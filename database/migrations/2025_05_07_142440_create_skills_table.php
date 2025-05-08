<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // informática, marketing, automoción
            $table->timestamps();

            $table->unique(['name', 'category']); // Evita duplicados en una misma categoría
        });
    }

    public function down()
    {
        Schema::dropIfExists('skills');
    }
};
