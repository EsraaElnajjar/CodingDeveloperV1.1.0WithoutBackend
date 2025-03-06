<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    protected $table = 'get';

    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // Create the 'get' table
        Schema::create('get', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('courses_id')->nullable();
            $table->string('phone');
            $table->string('image')->nullable();
            $table->timestamps();
        });
        // Add Foreign Keys using alter
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('get_id')->references('id')->on('get')->onDelete('set null');
        });

        Schema::table('get', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('courses_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('get');
    }
};
