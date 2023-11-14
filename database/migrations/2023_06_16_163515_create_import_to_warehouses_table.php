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
        Schema::create('import_to_warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('importcode');
            $table->string("date");
            $table->string("invoicedate");
            $table->string("invoiceno");
            $table->longText("photo");
            $table->string("totalamount");
            $table->longText("remark")->nullable();
            $table->foreignId("vendor_id")->reference("id")->on("vendors")->onDelete("cascade");
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
        Schema::dropIfExists('import_to_warehouses');
    }
};
