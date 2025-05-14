<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainAnalysesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_id')->constrained()->onDelete('cascade');
            $table->integer('seo_score');
            $table->integer('website_authority');
            $table->integer('backlinks_count')->nullable();
            $table->integer('website_speed')->nullable();
            $table->text('issues')->nullable()->comment('JSON array of issues');
            $table->text('recommendations')->nullable()->comment('JSON array of recommendations');
            $table->longText('raw_data')->nullable()->comment('Raw analysis data in JSON format');
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
        Schema::dropIfExists('domain_analyses');
    }
}
