<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('metal_id')->constrained('metals');
            $table->string('movementable_type');
            $table->unsignedBigInteger('movementable_id');
            $table->string('type', 20);
            $table->decimal('weight_grams', 15, 4);
            $table->decimal('purity', 5, 4);
            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();

            $table->index(['movementable_type', 'movementable_id']);
            $table->index(['metal_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
