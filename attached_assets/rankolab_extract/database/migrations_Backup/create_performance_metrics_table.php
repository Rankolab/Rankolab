<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
                Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->string('keyword');
            $table->integer('position')->nullable();
            $table->integer('clicks')->nullable();
            $table->integer('impressions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('performance_metrics');
    }
};