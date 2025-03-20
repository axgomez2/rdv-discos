<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_label')->nullable()->after('status');
            $table->string('tracking_code')->nullable()->after('shipping_label');
            $table->timestamp('shipped_at')->nullable()->after('tracking_code');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_label', 'tracking_code', 'shipped_at', 'delivered_at']);
        });
    }
};
