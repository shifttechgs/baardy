<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Promotions published from the admin panel.
     *
     * `product` holds the name of one of config('marketing.products') -- the
     * loans are defined in config, not in the database. The three `show_*`
     * flags choose where on the site a live promotion appears. A promotion is
     * live when it is approved and now falls between `starts_at` and
     * `ends_at`; both dates are required, so nothing can run indefinitely.
     */
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary', 80);
            $table->text('body');
            $table->text('terms');
            $table->string('image_path')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('product')->nullable();
            $table->string('cta_label', 40)->default('Visit a branch');
            $table->string('tracking_code', 40)->unique();
            $table->boolean('show_in_hero')->default(false);
            $table->boolean('show_in_menu')->default(false);
            $table->boolean('show_on_product')->default(false);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status', 32)->default('draft')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
