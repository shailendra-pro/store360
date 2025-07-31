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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->foreignId('subcategory_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('users')->onDelete('cascade'); // null = global
            $table->boolean('is_active')->default(true);
            $table->boolean('is_global')->default(false); // visible to all companies
            $table->decimal('price', 10, 2)->nullable();
            $table->string('sku')->nullable()->unique();
            $table->integer('stock_quantity')->default(0);
            $table->json('specifications')->nullable(); // for additional product specs
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['company_id', 'is_active']);
            $table->index(['is_global', 'is_active']);
            $table->index(['subcategory_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
