<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('uom');
            $table->string('sku');
            $table->string('qty')->default(0);
            $table->string('buyingprice')->nullable();
            $table->string('sellingprice')->nullable();
            $table->longText('photo')->nullable();
            // $table->foreignId('category_id')->reference('id')->on('categories')->onDelete('cascade');
            // $table->foreignId('brand_id')->reference('id')->on('brands')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
