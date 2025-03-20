<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('vinyl_quantity')->comment('Number of vinyls per month');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained('subscription_packages');
            $table->string('status');
            $table->string('mercadopago_subscription_id')->nullable();
            $table->timestamp('next_billing_date')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('subscription_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->constrained();
            $table->string('tracking_code')->nullable();
            $table->string('shipping_label_url')->nullable();
            $table->decimal('shipping_cost', 10, 2);
            $table->string('status');
            $table->date('shipping_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_shipments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_packages');
    }
};
