<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            if (! Schema::hasColumn('products', 'product_code')) {
                $table->string('product_code')->nullable()->after('id');
            }

            if (! Schema::hasColumn('products', 'customer_price')) {
                $table->decimal('customer_price', 10, 2)->nullable()->after('price');
            }
        });

        Schema::table('order_items', function (Blueprint $table): void {
            if (Schema::hasColumn('order_items', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->change();
            }

            if (! Schema::hasColumn('order_items', 'product_code')) {
                $table->string('product_code')->nullable()->after('product_id');
            }

            if (! Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->nullable()->after('product_code');
            }

            if (! Schema::hasColumn('order_items', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('product_name');
            }

            if (! Schema::hasColumn('order_items', 'customer_price')) {
                $table->decimal('customer_price', 10, 2)->nullable()->after('line_total');
            }

            if (! Schema::hasColumn('order_items', 'customer_line_total')) {
                $table->decimal('customer_line_total', 10, 2)->nullable()->after('customer_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            foreach (['product_code', 'product_name', 'expiry_date', 'customer_price', 'customer_line_total'] as $column) {
                if (Schema::hasColumn('order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('products', function (Blueprint $table): void {
            foreach (['product_code', 'customer_price'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
