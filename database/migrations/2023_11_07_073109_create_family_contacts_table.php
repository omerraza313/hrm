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
        Schema::create('family_contacts', function (Blueprint $table) {
            $relation = ['Brother', 'Sister', 'Spouse', 'Mother', 'Father'];
            $table->id();
            $table->string('name');
            $table->string('number');
            $table->string('relation');
            $table->string('dob')->nullable();
            $table->boolean('ice_status');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
    }
};
