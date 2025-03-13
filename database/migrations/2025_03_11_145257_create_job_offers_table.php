<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Relación con la empresa
            $table->string('title');
            $table->text('description');
            $table->integer('min_experience')->default(0); // Nueva columna
            $table->string('min_studies')->nullable(); // Nueva columna
            $table->integer('vacancies')->default(1); // Nueva columna
            $table->decimal('minimum_salary', 10, 2)->nullable(); // Renombrado desde salary_min
            $table->decimal('maximum_salary', 10, 2)->nullable(); // Renombrado desde salary_max
            $table->string('location')->nullable();
            $table->string('work_mode')->nullable(); // Nueva columna (antes era job_type)
            $table->date('expires_at')->nullable();
            $table->string('status')->default('active');
            $table->string('category')->nullable();
            $table->string('file')->nullable(); // Nueva columna para archivos
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_offers');
    }
};
