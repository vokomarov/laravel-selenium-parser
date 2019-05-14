<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('price_main')->unsigned()->default(0);
            $table->integer('price_decimal')->unsigned()->default(0);
            $table->decimal('rating', 3, 2)->unsigned()->default(0.00);
            $table->text('description')->nullable()->default(null);
            $table->string('imageUrl')->nullable()->default(null);
            $table->timestamps();
        });
    }
}
