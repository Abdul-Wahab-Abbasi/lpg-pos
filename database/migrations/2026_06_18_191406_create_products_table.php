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
            $table->string('name');
            $table->string('category')->default('Cylinder');
            $table->decimal('sale_price', 10, 2);
            $table->decimal('refill_charge', 10, 2);
            $table->decimal('return_deposit', 10, 2);
            $table->enum('unit', ['pcs', 'kg', 'ltr'])->default('pcs');
            $table->unsignedInteger('qty')->default(0);
            $table->unsignedInteger('min_qty')->default(0);
            $table->unsignedInteger('max_qty')->default(0);
            $table->timestamps();
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
