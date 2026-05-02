<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained()->restrictOnDelete();
            $table->date('invoice_date');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
