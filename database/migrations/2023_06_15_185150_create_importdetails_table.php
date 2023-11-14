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
        Schema::create('importdetails', function (Blueprint $table) {
            $table->id();
            // $table->string("uom");
            $table->string("qty");
            $table->string("rate");
            $table->integer("productamount");
            $table->foreignId('product_id')->reference('id')->on('products')->onDelete('cascade');
            $table->foreignId('importtowarehouse_id')->reference('id')->on('importtowarehouses')->onDelete('cascade');
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
        Schema::dropIfExists('importdetails');
    }
};
