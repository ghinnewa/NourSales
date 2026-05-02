<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('bonus_eligible')->default(false);
            $table->text('bonus_notes')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('bonus_quantity')->default(0);
            $table->text('bonus_notes')->nullable();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('is_cash_bonus')->default(false);
            $table->boolean('is_single_transaction_bonus')->default(false);
            $table->text('bonus_notes')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->text('deal_notes')->nullable();
            $table->text('internal_notes')->nullable();
        });

        Schema::table('pharmacies', function (Blueprint $table) {
            $table->text('deal_notes')->nullable();
            $table->text('payment_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            $table->dropColumn(['deal_notes', 'payment_notes']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['deal_notes', 'internal_notes']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['is_cash_bonus', 'is_single_transaction_bonus', 'bonus_notes']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['bonus_quantity', 'bonus_notes']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['bonus_eligible', 'bonus_notes']);
        });
    }
};
