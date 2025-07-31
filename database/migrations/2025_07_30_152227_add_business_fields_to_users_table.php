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
            // Business-specific fields
            $table->string('business_name')->nullable()->after('name');
            $table->string('contact_email')->nullable()->after('email');
            $table->string('username')->unique()->nullable()->after('contact_email');
            $table->string('logo_path')->nullable()->after('username');
            $table->boolean('is_active')->default(true)->after('logo_path');
            $table->timestamp('expires_at')->nullable()->after('is_active');
            $table->text('business_description')->nullable()->after('expires_at');
            $table->string('phone')->nullable()->after('business_description');
            $table->string('address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('address');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->nullable()->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'contact_email',
                'username',
                'logo_path',
                'is_active',
                'expires_at',
                'business_description',
                'phone',
                'address',
                'city',
                'state',
                'postal_code',
                'country'
            ]);
        });
    }
};
