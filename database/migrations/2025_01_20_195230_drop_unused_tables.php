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
        Schema::dropIfExists('users1');
        Schema::dropIfExists('users_details1');
        Schema::dropIfExists('policy_user1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
