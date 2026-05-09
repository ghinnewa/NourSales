<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('request_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('product_name_snapshot');
            $table->decimal('product_price_snapshot', 12, 2)->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('line_total', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_invoice_items');
    }
};
