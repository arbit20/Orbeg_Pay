<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shippable_type');
            $table->unsignedBigInteger('shippable_id');
            $table->foreignId('user_id')->constrained('users');
            $table->string('tracking_number', 100)->nullable();
            $table->string('carrier', 150)->nullable();
            $table->string('origin_address', 500);
            $table->string('destination_address', 500);
            $table->date('shipped_date')->nullable();
            $table->date('estimated_delivery')->nullable();
            $table->date('actual_delivery')->nullable();
            $table->string('status', 20)->default('preparando');
            $table->decimal('total_weight_grams', 15, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['shippable_type', 'shippable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
