<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Job vacancies published from the admin panel. A vacancy is open when it
     * is published and has not passed its closing date (blank = open until
     * unpublished).
     */
    public function up(): void
    {
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('location');
            $table->string('employment_type', 40)->default('Full-time');
            $table->string('summary', 160);
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->date('closes_on')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
    }
};
