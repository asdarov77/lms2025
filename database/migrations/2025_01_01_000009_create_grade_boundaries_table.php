<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_boundaries', function (Blueprint $table) {
            $table->id();
            $table->integer('boundary');
            $table->integer('grade');
            $table->timestamps();
        });
        
        // Insert default boundaries: 0→2, 35→3, 65→4, 85→5
        DB::table('grade_boundaries')->insert([
            ['boundary' => 0, 'grade' => 2],
            ['boundary' => 35, 'grade' => 3],
            ['boundary' => 65, 'grade' => 4],
            ['boundary' => 85, 'grade' => 5],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_boundaries');
    }
};
