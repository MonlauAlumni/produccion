<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('job_offer_skill', function (Blueprint $table) {
            $table->foreignId('job_offer_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->primary(['job_offer_id', 'skill_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_offer_skill');
    }
};
