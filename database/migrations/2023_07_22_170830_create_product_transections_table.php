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
        Schema::create('product_transections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_to_warehouse_id')->reference('id')->on('importtowarehouses')->onDelete('cascade')->nullable();
            $table->foreignId('sale_invoice_id')->reference('id')->on('sale_invoices')->onDelete('cascade')->nullable();
            $table->foreignId('product_conversion_id')->reference('id')->on('product_conversions')->onDelete('cascade')->nullable();
            $table->string('type');
            $table->foreignId('product_id')->reference('id')->on('products')->onDelete('cascade');
            $table->string('opening');
            $table->string('in')->default(0);
            $table->string('out')->default(0);
            $table->string('closing');
            $table->foreignId("created_by")->reference("id")->on("users")->onDelete('cascade')->nullable();
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
        Schema::dropIfExists('product_transections');
    }
};
