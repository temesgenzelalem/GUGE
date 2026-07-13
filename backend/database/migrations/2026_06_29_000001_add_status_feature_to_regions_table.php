<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            if (! Schema::hasColumn('regions', 'status')) {
                $table->string('status')->default('published')->after('direction');
            }
            if (! Schema::hasColumn('regions', 'featured')) {
                $table->boolean('featured')->default(false)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            if (Schema::hasColumn('regions', 'featured')) {
                $table->dropColumn('featured');
            }
            if (Schema::hasColumn('regions', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
