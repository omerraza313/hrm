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
        Schema::create('leave_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('c_from_date');
            $table->string('c_to_date');
            $table->string('quantity');
            $table->string('carry_forward')->nullable();
            $table->string('consective_leaves')->nullable();
            $table->string('apply_after_year');
            $table->string('apply_after_month');
            $table->string('leave_gender_type');
            $table->unsignedBigInteger('leave_type_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();


            $table->foreign('leave_type_id')->references('id')->on('leave_types')->nullOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_plans');
    }
};
