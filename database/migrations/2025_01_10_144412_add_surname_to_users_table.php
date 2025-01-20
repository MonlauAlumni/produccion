<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSurnameToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
         
            $table->string('last_name_1')->after('name');
            $table->string('last_name_2')->nullable()->after('last_name_1'); 
            $table->enum('training_area', ['Informatica', 'Marketing', 'Automocion'])->after('role');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
           
            $table->dropColumn(['last_name_1', 'last_name_2', 'training_area']);
        });
    }
}
