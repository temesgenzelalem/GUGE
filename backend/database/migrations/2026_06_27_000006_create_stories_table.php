<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->unsignedInteger('region_id');
            $table->unsignedInteger('creator_id')->nullable();
            $table->string('type');
            $table->text('excerpt');
            $table->text('body');
            $table->string('wiki_article');
            $table->string('image_url')->nullable();
            $table->unsignedInteger('read_minutes')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('creators')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
