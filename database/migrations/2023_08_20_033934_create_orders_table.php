<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
			$table->string('customer');
			$table->tinyInteger('paid')->nullable()->default(0);
			$table->decimal('total_amount',10,2)->nullable()->default(0);
			$table->decimal('discount_amount',10,2)->nullable()->default(0);
			$table->decimal('payable_amount',10,2)->nullable()->default(0);
			$table->decimal('paid_amount',10,2)->nullable()->default(0);
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
        Schema::dropIfExists('orders');
    }
}
