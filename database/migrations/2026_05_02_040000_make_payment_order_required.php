<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::table('payments')->whereNull('order_id')->delete();

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->change();
        });
    }
};
