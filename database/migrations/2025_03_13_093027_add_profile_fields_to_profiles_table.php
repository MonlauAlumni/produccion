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
        Schema::table('profiles', function (Blueprint $table) {
            $table->integer('phone')->nullable();
            $table->enum('availability', [
                'Disponible para ofertas',
                'Abierto a oportunidades',
                'No disponible actualmente',
                'Solo ofertas remotas',
                'Solo ofertas a tiempo parcial'
                ])->nullable();
            $table->string('degree')->nullable();
            $table->string('job_title')->nullable();
            $table->integer('graduation_year')->nullable();
            $table->string('location')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->string('website')->nullable();
            $table->string('slang')->nullable();
            $table->string('banner_url')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('availability');
            $table->dropColumn('degree');
            $table->dropColumn('job_title');
            $table->dropColumn('graduation_year');
            $table->dropColumn('location');
            $table->dropColumn('linkedin');
            $table->dropColumn('github');
            $table->dropColumn('website');
            $table->dropColumn('slang');
            $table->dropColumn('banner_url');

        });
    }
};
