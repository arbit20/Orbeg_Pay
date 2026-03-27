<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number', 30)->unique();
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('user_id')->constrained('users');
            $table->date('sale_date');
            $table->dateTime('sale_datetime')->nullable();
            $table->unsignedInteger('lot_count')->default(1);
            $table->decimal('weight_grams', 15, 4)->nullable();
            $table->decimal('purity', 5, 4)->nullable();
            $table->decimal('unit_price_bs', 15, 4)->nullable();
            $table->decimal('total_bs', 15, 4)->nullable();
            $table->decimal('reference_quote_usd_oz', 15, 4)->nullable();
            $table->string('quote_source', 50)->nullable();
            $table->decimal('exchange_rate_bs_usd', 10, 4)->nullable();
            $table->string('evidence_path', 500)->nullable();
            $table->string('status', 20)->default('borrador');
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
