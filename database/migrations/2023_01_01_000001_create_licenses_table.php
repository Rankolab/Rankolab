<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('license_key')->unique();
            $table->enum('plan', ['free', 'basic', 'pro', 'enterprise'])->default('free');
            $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])->default('pending');
            $table->integer('max_websites')->default(1);
            $table->integer('max_content_per_month')->default(5);
            $table->timestamp('expires_at')->nullable();
            $table->text('domains')->nullable()->comment('JSON array of registered domains');
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
        Schema::dropIfExists('licenses');
    }
}
