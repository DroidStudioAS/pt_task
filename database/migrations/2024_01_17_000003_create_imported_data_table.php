<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('imported_data', function (Blueprint $table) {
            $table->id();
            $table->string('import_type');
            $table->date('order_date');
            $table->string('channel');
            $table->string('sku');
            $table->text('item_description');
            $table->string('origin');
            $table->string('so_number');
            $table->decimal('total_price', 10, 2);
            $table->decimal('cost', 10, 2);
            $table->decimal('shipping_cost', 10, 2);
            $table->decimal('profit', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('imported_data');
    }
}; 