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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code");
            $table->string("type");
            $table->string("paymenttype");
            $table->boolean("is_active");
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
        Schema::dropIfExists('vendors');
    }
};
