<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metal_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('metal_id')->constrained('metals')->cascadeOnDelete();
            $table->decimal('price_per_gram', 15, 4);
            $table->decimal('price_per_troy_ounce', 15, 4);
            $table->string('currency', 3)->default('USD');
            $table->date('effective_date');
            $table->string('source', 100)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['metal_id', 'currency', 'effective_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metal_prices');
    }
};
