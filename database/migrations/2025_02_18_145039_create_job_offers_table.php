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
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('location')->nullable();
            $table->enum('job_type', ['onsite', 'remote', 'hybrid'])->default('onsite');
            $table->dateTime('expires_at')->nullable();
            $table->enum('status', ['active', 'expired', 'closed'])->default('active');
            $table->enum('category', ['marketing', 'automotive', 'it'])->default('it');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            //
        });
    }
};
