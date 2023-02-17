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
        Schema::create('sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('saledate');
            $table->string('orderno');
            $table->string('totalamount');
            $table->string('remark')->nullable();
            $table->foreignId("created_by")->reference("id")->on("users")->onDelete('cascade')->nullable();
            $table->foreignId("updated_by")->reference("id")->on("users")->onDelete('cascade')->nullable();
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
        Schema::dropIfExists('sale_invoices');
    }
};
