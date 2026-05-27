<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aircraft_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('path');
            $table->string('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->boolean('visible')->default(true);
            $table->timestamps();
            
            $table->index(['aircraft_id', 'visible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
