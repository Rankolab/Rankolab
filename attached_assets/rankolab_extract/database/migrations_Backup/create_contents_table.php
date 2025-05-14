<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
                Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->foreignId('content_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->longText('body');
            $table->json('images')->nullable();
            $table->json('links')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('contents');
    }
};