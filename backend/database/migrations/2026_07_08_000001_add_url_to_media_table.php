<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('disk', 20)->default('public')->after('path');
            $table->string('url')->nullable()->after('disk');
            $table->string('original_filename')->nullable()->after('filename');
            $table->string('collection', 80)->nullable()->after('original_filename');
            $table->json('conversions')->nullable()->after('metadata');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['disk', 'url', 'original_filename', 'collection', 'conversions']);
        });
    }
};
