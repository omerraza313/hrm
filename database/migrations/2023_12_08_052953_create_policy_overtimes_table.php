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
        Schema::create('policy_overtimes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id')->nullable();
            $table->string('ot_status');
            $table->string('ot_start')->nullable();
            $table->string('ot_min_minutes')->nullable();
            $table->string('ot_rate_status')->nullable();
            $table->string('ot_rate')->nullable();
            $table->string('ot_amount')->nullable();

            $table->foreign('policy_id')->references('id')->on('policies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_overtimes');
    }
};
