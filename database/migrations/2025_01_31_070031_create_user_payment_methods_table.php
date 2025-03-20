<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('user_payment_methods')) {
            Schema::create('user_payment_methods', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
                $table->string('provider')->nullable();
                $table->string('account_number')->nullable();
                $table->string('expiration_date')->nullable();
                $table->string('pix_key_type')->nullable();
                $table->string('pix_key')->nullable();
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        Schema::table('user_payment_methods', function (Blueprint $table) {
            $table->unique(['user_id', 'payment_method_id', 'account_number'], 'upm_user_pm_account_unique');
            $table->unique(['user_id', 'payment_method_id', 'pix_key'], 'upm_user_pm_pix_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_payment_methods');
    }
};
