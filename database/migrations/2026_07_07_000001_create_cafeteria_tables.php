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
        Schema::create('cafeteria_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('cafeteria_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('cafeteria_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('wholesale_price', 10, 2)->default(0);
            $table->decimal('distribute_price', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cafeteria_sales', function (Blueprint $table) {
            $table->id();
            $table->string('billing_no')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('users_user')->onDelete('set null');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('grand_total', 10, 2);
            $table->string('payment_method')->default('cash');
            $table->string('payment_status')->default('paid');
            $table->string('price_type')->default('retail');
            $table->timestamps();
        });

        Schema::create('cafeteria_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('cafeteria_sales')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('cafeteria_products')->onDelete('set null');
            $table->string('product_name');
            $table->string('product_code')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafeteria_sale_items');
        Schema::dropIfExists('cafeteria_sales');
        Schema::dropIfExists('cafeteria_products');
        Schema::dropIfExists('cafeteria_categories');
    }
};
