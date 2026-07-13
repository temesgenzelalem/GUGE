<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('region_id')->nullable();
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->text('story')->nullable();
            $table->string('wiki_article')->nullable();
            $table->string('image_url')->nullable();
            $table->json('tags')->nullable();
            $table->text('how_to_order')->nullable();
            $table->timestamps();

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
