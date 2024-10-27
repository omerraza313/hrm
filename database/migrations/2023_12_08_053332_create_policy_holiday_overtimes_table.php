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
        Schema::create('policy_holiday_overtimes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id')->nullable();
            $table->string('holiday_ot_status');
            $table->string('holiday_ot_rate')->nullable();
            $table->string('holiday_ot_amount')->nullable();

            $table->foreign('policy_id')->references('id')->on('policies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_holiday_overtimes');
    }
};
