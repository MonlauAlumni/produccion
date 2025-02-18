<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('job_offer_id');
            $table->dateTime('application_date')->useCurrent();

            $table->enum('status', ['pendiente', 'aceptada', 'rechazada'])->default('pendiente');
            
            $table->foreign('student_id', 'applications_ibfk_1')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('job_offer_id', 'applications_ibfk_2')->references('id')->on('job_offers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
}
