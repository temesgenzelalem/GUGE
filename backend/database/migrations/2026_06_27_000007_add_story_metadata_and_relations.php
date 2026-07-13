<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->string('category')->nullable()->after('creator_id');
            $table->text('content')->nullable()->after('excerpt');
            $table->string('featured_image')->nullable()->after('wiki_article');
            $table->json('gallery')->nullable()->after('featured_image');
            $table->string('status')->default('published')->after('category');
            $table->boolean('featured')->default(false)->after('status');
            $table->string('language')->default('en')->after('featured');
            $table->string('seo_title')->nullable()->after('language');
            $table->string('seo_description')->nullable()->after('seo_title');
            $table->unsignedInteger('view_count')->default(0)->after('published_at');
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('story_tag', function (Blueprint $table) {
            $table->unsignedInteger('story_id');
            $table->unsignedInteger('tag_id');
            $table->primary(['story_id', 'tag_id']);
            $table->foreign('story_id')->references('id')->on('stories')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });

        Schema::create('product_story', function (Blueprint $table) {
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('story_id');
            $table->primary(['product_id', 'story_id']);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('story_id')->references('id')->on('stories')->onDelete('cascade');
        });

        if (Schema::hasColumn('stories', 'type')) {

            DB::table('stories')->whereNotNull('type')->chunkById(100, function ($stories) {
                foreach ($stories as $story) {
                    DB::table('stories')->where('id', $story->id)->update(['category' => $story->type]);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->dropColumn(['category', 'content', 'featured_image', 'gallery', 'status', 'featured', 'language', 'seo_title', 'seo_description', 'view_count']);
        });

        Schema::dropIfExists('product_story');
        Schema::dropIfExists('story_tag');
        Schema::dropIfExists('tags');
    }
};
