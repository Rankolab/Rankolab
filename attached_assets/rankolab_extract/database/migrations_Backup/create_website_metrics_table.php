<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
                Schema::create('website_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->integer('seo_score')->nullable();
            $table->integer('domain_authority')->nullable();
            $table->integer('backlinks')->nullable();
            $table->float('page_speed')->nullable();
            $table->json('additional_metrics')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('website_metrics');
    }
};