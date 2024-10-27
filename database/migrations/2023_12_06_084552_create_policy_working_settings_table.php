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
        Schema::create('policy_working_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('policy_id');
            $table->string('shift_start');
            $table->string('shift_close');
            $table->string('late_c_l_t');
            $table->string('early_arrival_policy');
            $table->string('force_timeout');
            $table->string('timeout_policy');
            $table->string('late_minute_monthly_bucket');
            $table->string('late_comers_penalty');

            $table->foreign('policy_id')->references('id')->on('policies')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policy_working_settings');
    }
};
