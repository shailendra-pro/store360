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
        Schema::table('users', function (Blueprint $table) {
            $table->string('secure_link')->nullable()->after('role');
            $table->timestamp('secure_link_expires_at')->nullable()->after('secure_link');
            $table->integer('custom_hours')->nullable()->after('secure_link_expires_at');
            $table->string('company')->nullable()->after('custom_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['secure_link', 'secure_link_expires_at', 'custom_hours', 'company']);
        });
    }
};
