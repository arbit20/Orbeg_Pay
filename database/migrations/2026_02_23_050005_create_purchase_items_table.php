<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->foreignId('metal_id')->constrained('metals');
            $table->string('description', 255)->nullable();
            $table->decimal('weight_grams', 15, 4);
            $table->decimal('purity', 5, 4);
            $table->decimal('price_per_gram', 15, 4);
            $table->decimal('subtotal', 15, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
