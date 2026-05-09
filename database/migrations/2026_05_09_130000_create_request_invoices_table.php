<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('request_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('requester_name');
            $table->string('pharmacy_name');
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->text('whatsapp_message')->nullable();
            $table->decimal('total_amount', 12, 2)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_invoices');
    }
};
