<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number', 20)->unique();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('metal_id')->constrained('metals');
            $table->decimal('estimated_weight_grams', 15, 4);
            $table->decimal('estimated_purity', 5, 4)->nullable();
            $table->decimal('quoted_price_per_gram', 15, 4);
            $table->decimal('estimated_total', 15, 4);
            $table->string('currency', 3)->default('USD');
            $table->string('status', 20)->default('pendiente');
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('client_notes')->nullable();
            $table->text('operator_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
