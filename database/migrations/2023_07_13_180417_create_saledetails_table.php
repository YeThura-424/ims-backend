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
        Schema::create('saledetails', function (Blueprint $table) {
            $table->id();
            $table->string("qty");
            $table->string("rate");
            $table->string("productamount");
            $table->foreignId('product_id')->reference('id')->on('products')->onDelete('cascade');
            $table->foreignId('saleinvoice_id')->reference('id')->on('sale_invoices')->onDelete('cascade');
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
        Schema::dropIfExists('saledetails');
    }
};
