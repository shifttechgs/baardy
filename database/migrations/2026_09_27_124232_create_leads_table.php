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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 16)->unique();

            $table->string('name', 120);
            $table->string('phone', 40);
            // Digits only, with the country code: matching repeat enquiries
            // and building WhatsApp links.
            $table->string('phone_normalized', 20)->index();
            $table->string('email', 190)->nullable();
            $table->string('interest', 80);
            $table->string('branch', 60);
            $table->text('message')->nullable();

            $table->string('stage', 20)->default('new')->index();
            $table->string('lost_reason', 30)->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('first_contacted_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('last_enquired_at')->nullable();

            // Where the lead came from.
            $table->foreignId('promotion_id')->nullable()->constrained()->nullOnDelete();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('referrer', 255)->nullable();
            $table->string('landing_page', 255)->nullable();

            $table->timestamps();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
