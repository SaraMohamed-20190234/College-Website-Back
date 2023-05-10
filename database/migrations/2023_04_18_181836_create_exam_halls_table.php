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
        Schema::create('exam_halls', function (Blueprint $table) {
            $table->id();
            $table->integer('Student_id');
            $table->string('Subject_code');
            $table->string('Subject_name');
            $table->string('Exam_Hall');
            $table->integer('Student_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_halls');
    }
};
