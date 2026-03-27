<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->string('settlement_number', 20)->unique();
            $table->string('settleable_type');
            $table->unsignedBigInteger('settleable_id');
            $table->foreignId('user_id')->constrained('users');
            $table->date('settlement_date');
            $table->decimal('total_amount', 15, 4);
            $table->string('currency', 3)->default('USD');
            $table->string('status', 20)->default('pendiente');
            $table->string('pdf_path', 500)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['settleable_type', 'settleable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
