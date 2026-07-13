<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creators', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('full_name');
            $table->string('username')->unique()->nullable();
            $table->string('slug')->unique();
            $table->unsignedInteger('region_id')->nullable();
            $table->string('role')->nullable();
            $table->text('bio')->nullable();
            $table->string('status')->default('published');
            $table->json('specialties')->nullable();
            $table->json('languages')->nullable();
            $table->json('social_links')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('website_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('wiki_article')->nullable();
            $table->string('image_url')->nullable();
            $table->float('rating')->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedInteger('story_count')->default(0);
            $table->unsignedInteger('product_count')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creators');
    }
};
