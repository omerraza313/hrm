<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_adjusts', function (Blueprint $table) {
            $table->id();
            $table->string('leave_date');
            $table->unsignedBigInteger('leave_plan_id')->nullable();
            $table->unsignedBigInteger('applyleave_id')->nullable();
            $table->string('quantity')->default(1);

            $table->foreign('leave_plan_id')->references('id')->on('leave_plans')->onDelete('cascade');
            $table->foreign('applyleave_id')->references('id')->on('applyleaves')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_adjusts');
    }
};