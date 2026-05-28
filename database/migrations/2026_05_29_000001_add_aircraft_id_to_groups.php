<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->foreignId('aircraft_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['aircraft_id']);
            $table->dropColumn('aircraft_id');
        });
    }
};
