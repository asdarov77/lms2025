<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
        });

        Schema::create('permissions_users', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'user_id']);
        });

        Schema::create('permissions_roles', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        // Add group_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('group_id')->nullable()->after('email')->constrained()->nullOnDelete();
            $table->string('fio')->after('name');
            $table->string('role')->nullable()->after('fio');
            $table->string('phonenumber')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('organization')->nullable();
            $table->string('position')->nullable();
            $table->string('rank')->nullable();
            $table->string('spfere')->nullable();
            $table->string('specialization')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->dropColumn([
                'group_id', 'fio', 'role', 'phonenumber', 'city', 'country',
                'organization', 'position', 'rank', 'spfere', 'specialization'
            ]);
        });
        
        Schema::dropIfExists('permissions_roles');
        Schema::dropIfExists('permissions_users');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
