<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('target_keywords');
            $table->integer('min_words')->default(1000);
            $table->integer('word_count')->nullable();
            $table->enum('status', ['pending', 'generated', 'published', 'failed'])->default('pending');
            $table->float('plagiarism_score')->nullable();
            $table->float('readability_score')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('quality_checked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contents');
    }
}
