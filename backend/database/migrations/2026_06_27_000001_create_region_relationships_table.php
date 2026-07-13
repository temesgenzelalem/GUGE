<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('region_relationships', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('source_region_id');
            $table->string('target_type', 80);
            $table->unsignedInteger('target_id');
            $table->string('target_name', 200);
            $table->float('weight')->default(0.0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['source_region_id', 'target_type']);
            $table->index(['source_region_id', 'target_id']);
            $table->foreign('source_region_id')->references('id')->on('regions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('region_relationships');
    }
};
