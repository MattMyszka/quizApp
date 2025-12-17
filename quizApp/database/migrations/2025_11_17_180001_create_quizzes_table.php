<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('num_questions')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('quizzes');
    }
};
