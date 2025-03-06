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
        Schema::create('teach', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('courses_id')->nullable();
            $table->unsignedBigInteger('lecturer_id')->nullable();
            $table->timestamps();
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->foreign('teach_id')->references('id')->on('teach')->onDelete('set null');
            $table->foreign('get_id')->references('id')->on('get')->onDelete('set null');
        });
        Schema::table('lecturer', function (Blueprint $table) {
            $table->foreign('teach_id')->references('id')->on('teach')->onDelete('set null');
        });
        Schema::table('teach', function (Blueprint $table) {
            $table->foreign('courses_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('lecturer_id')->references('id')->on('lecturer')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teach');
    }
};
