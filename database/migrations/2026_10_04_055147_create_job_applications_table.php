<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Applications with a CV. `vacancy_id` is null for a general application
     * ("keep me in mind"). The CV itself is kept in `cv_data` (base64, so it is portable across
     * databases and never reachable by URL) and is also emailed to the team.
     */
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone', 40);
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->string('cv_original_name');
            $table->string('cv_mime', 100);
            $table->unsignedInteger('cv_size');
            $table->longText('cv_data');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
