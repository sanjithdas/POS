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
        // Schema::dropIfExists('permissions');
        // Schema::dropIfExists('roles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the tables if needed
        // Schema::create('roles', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('role_type');
        //     $table->foreignId('user_id')->constrained()->onDelete('cascade');
        //     $table->timestamps();
        // });

        // Schema::create('permissions', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
        //     $table->string('permission_type');
        //     $table->timestamps();
        // });
    }
};

