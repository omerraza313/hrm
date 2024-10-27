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
        Schema::table('attendences', function (Blueprint $table) {
            $table->string('remarks')->nullable()->after('status'); // Adds the 'remarks' column
            $table->string('device_id')->nullable()->after('remarks'); // Adds the 'device_id' column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendences', function (Blueprint $table) {
            $table->dropColumn('remarks');
            $table->dropColumn('device_id');
        });
    }
};
