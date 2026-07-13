<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('zone')->nullable();
            $table->string('direction', 10)->nullable();
            $table->text('description')->nullable();
            $table->string('tagline')->nullable();
            $table->string('wiki_article')->nullable();
            $table->string('image_url')->nullable();
            $table->json('tags')->nullable();
            $table->json('stats')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
