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
        Schema::create('vinyl_secs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vinyl_master_id');
            $table->string('catalog_number')->nullable();
            $table->string('barcode')->nullable();
            $table->unsignedBigInteger('weight_id')->nullable();
            $table->unsignedBigInteger('dimension_id')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('price', 10, 2);
            $table->string('format')->nullable();
            $table->integer('num_discs')->default(1);
            $table->string('speed')->nullable();
            $table->string('edition')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_new')->default(true);
            $table->decimal('buy_price', 10, 2)->nullable();
            $table->decimal('promotional_price', 10, 2)->nullable();
            $table->boolean('is_promotional')->default(false);
            $table->boolean('in_stock')->default(true);
            $table->enum('cover_status', ['mint', 'near_mint', 'very_good', 'good', 'fair', 'poor', 'generic'])->nullable();
            $table->enum('midia_status', ['mint', 'near_mint', 'very_good', 'good', 'fair', 'poor'])->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vinyl_master_id')->references('id')->on('vinyl_masters')->onDelete('cascade');
            $table->foreign('weight_id')->references('id')->on('weights')->onDelete('set null');
            $table->foreign('dimension_id')->references('id')->on('dimensions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vinyl_secs');
    }
};
