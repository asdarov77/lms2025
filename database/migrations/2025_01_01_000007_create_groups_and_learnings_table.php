<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('groupname');
            $table->text('groupdescription')->nullable();
            $table->timestamps();
        });

        Schema::create('group2learnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('group2learnings')->nullOnDelete();
            $table->string('teacher')->nullable();
            $table->string('typeOfLesson')->nullable();
            $table->date('study_from');
            $table->date('study_to');
            $table->timestamps();
            
            $table->index(['group_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group2learnings');
        Schema::dropIfExists('groups');
    }
};
