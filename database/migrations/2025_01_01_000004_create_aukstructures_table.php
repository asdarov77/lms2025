<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aukstructures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('aukstructures')->nullOnDelete();
            $table->string('title');
            $table->tinyInteger('type')->default(0); // 0=Курс, 1=Тема, 2=Раздел, 3=Модуль
            $table->string('description')->nullable();
            $table->string('identifier')->nullable();
            $table->string('categories')->nullable();
            $table->timestamps();
            
            $table->index(['course_id', 'parent_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aukstructures');
    }
};
