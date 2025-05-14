<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_trackings', function (Blueprint $table) {
            $table->id();
            $table->morphs('trackable'); // Can be content or affiliate link
            $table->enum('event_type', ['impression', 'click', 'share', 'conversion'])->default('impression');
            $table->string('url')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('meta_data')->nullable()->comment('JSON metadata for the event');
            $table->float('value')->nullable()->comment('Value for conversion events');
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
        Schema::dropIfExists('content_trackings');
    }
}
