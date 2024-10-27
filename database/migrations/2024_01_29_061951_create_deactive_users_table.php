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
        Schema::create('deactive_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('notice_period_served')->default('0');
            $table->date('notice_period_date')->nullable();
            $table->string('notice_period_duration')->nullable();
            $table->date('exit_date')->nullable();
            $table->string('all_cleared')->nullable();
            $table->string('reason')->nullable();
            $table->string('comments')->nullable();


            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deactive_users');
    }
};