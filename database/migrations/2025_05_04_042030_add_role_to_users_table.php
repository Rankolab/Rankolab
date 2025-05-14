<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("users", function (Blueprint $table) {
            // Add role column, default to 'user', index for faster lookups
            $table->string("role")->default("user")->after("email");
            $table->index("role"); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {
            // Drop the index first, then the column
            $table->dropIndex(["role"]);
            $table->dropColumn("role");
        });
    }
};

