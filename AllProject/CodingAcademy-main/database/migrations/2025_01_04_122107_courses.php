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
        Schema::create('courses', function (Blueprint $table) {

        $table->id();
        $table->string('name');
        $table->string('contant');
        $table->text('description');
        $table->decimal('price', 10, 2);
        $table->string('time');
        $table->string('image')->nullable();
        $table->unsignedBigInteger('user_add_id')->nullable();
        $table->unsignedBigInteger('teach_id')->nullable();
        $table->unsignedBigInteger('get_id')->nullable();
        // $table->unsignedBigInteger('lecturer_id')->nullable();
        $table->timestamps();
    });
    Schema::table('courses', function (Blueprint $table) {
        $table->foreign('user_add_id')->references('id')->on('users')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');

    }
};
