<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applyleaves', function (Blueprint $table) {
            // $table->dropForeign(['applyleaves_user_id_foreign']);
            // $table->dropForeign(['applyleaves_approved_by_foreign']);
        });

        Schema::table('applyleaves', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applyleaves', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
