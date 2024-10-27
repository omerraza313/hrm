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
        Schema::create('policy_swap_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('current_policy')->nullable();
            $table->unsignedBigInteger('swap_policy')->nullable();
            $table->string('effect_date')->nullable();
            $table->string('effect_time')->nullable();
            $table->string('rollback_date')->nullable();
            $table->string('rollback_time')->nullable();
            $table->string('status')->nullable();

            $table->foreign('current_policy')->references('id')->on('policies')->onDelete('cascade');
            $table->foreign('swap_policy')->references('id')->on('policies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_swap_jobs');
    }
};
