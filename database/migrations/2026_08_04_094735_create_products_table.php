<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete()
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('name');

            $table->string('slug')->unique();

            $table->text('description')->nullable();

            $table->decimal('price',10,2);

            $table->decimal('discount_price',10,2)->nullable();

            $table->integer('stock')->default(0);

            $table->string('unit');

            // only filename
            $table->string('image')->nullable();

            $table->boolean('featured')->default(false);

            $table->boolean('status')->default(true);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};