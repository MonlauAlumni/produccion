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
        Schema::table('job_offers', function (Blueprint $table) {
            // Add only the fields that are still missing
            $table->text('requirements')->nullable()->after('description');
            $table->text('experience')->nullable()->after('requirements');
            $table->json('responsibilities')->nullable()->after('experience');
            $table->json('benefits')->nullable()->after('responsibilities');
            $table->date('deadline')->nullable()->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropColumn([
                'requirements',
                'experience',
                'responsibilities',
                'benefits',
                'deadline'
            ]);
        });
    }
};
