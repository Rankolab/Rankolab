<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
                Schema::create('content_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->string('keyword');
            $table->json('competitor_analysis')->nullable();
            $table->json('schedule')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('content_plans');
    }
};