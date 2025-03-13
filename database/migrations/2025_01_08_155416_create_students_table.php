<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cycle_id')->nullable();
            $table->year('graduation_year', 4)->nullable();
            $table->text('bio')->nullable();
            
            $table->foreign('user_id', 'students_ibfk_1')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cycle_id', 'students_ibfk_2')->references('id')->on('cycles')->onDelete('set NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
