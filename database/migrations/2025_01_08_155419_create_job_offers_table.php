<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('title');
            $table->text('description');
            $table->text('required_skills')->nullable();
            $table->string('salary_range', 50)->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_remote')->default(0);
            $table->dateTime('created_at')->useCurrent();


            $table->dateTime('expires_at')->nullable();
            
            $table->foreign('company_id', 'job_offers_ibfk_1')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_offers');
    }
}
