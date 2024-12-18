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
        Schema::table('assign_leaves', function (Blueprint $table) {
            $table->dropForeign('fk_assign_leaves_user');
        });

        Schema::table('assign_leaves', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assign_leaves', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
