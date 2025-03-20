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
        if (Schema::hasColumn('vinyl_secs', 'cat_style_shop_id')) {
            Schema::table('vinyl_secs', function (Blueprint $table) {
                try {
                    $table->dropForeign(['cat_style_shop_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                $table->dropColumn('cat_style_shop_id');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasColumn('vinyl_secs', 'cat_style_shop_id')) {
            Schema::table('vinyl_secs', function (Blueprint $table) {
                $table->unsignedBigInteger('cat_style_shop_id')->nullable();
                $table->foreign('cat_style_shop_id')->references('id')->on('cat_style_shop')->onDelete('set null');
            });
        }
    }
};
