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
        Schema::create('assign_leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leave_plan_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('remaining_leave');

            // Specify the name of the foreign key
            $table->foreign('leave_plan_id', 'fk_assign_leaves_leave_plan')->references('id')->on('leave_plans')->onDelete('cascade');
            $table->foreign('user_id', 'fk_assign_leaves_user')->references('id')->on('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Specify the name of the foreign key
        Schema::table('assign_leaves', function (Blueprint $table) {
            $table->dropForeign('fk_assign_leaves_leave_plan');
            $table->dropForeign('fk_assign_leaves_user');
        });

        Schema::dropIfExists('assign_leaves');
    }
};
