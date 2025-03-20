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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // Ex: 'credit_card', 'debit_card', 'bank_transfer', 'pix'
            $table->timestamps();
        });

        // Insert default payment methods
        DB::table('payment_methods')->insert([
            ['name' => 'Cartão de Crédito', 'type' => 'credit_card'],
            ['name' => 'Cartão de Débito', 'type' => 'debit_card'],
            ['name' => 'Transferência Bancária', 'type' => 'bank_transfer'],
            ['name' => 'PIX', 'type' => 'pix'],
        ]);
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
