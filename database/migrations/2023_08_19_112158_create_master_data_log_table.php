<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterDataLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_data_logs', function (Blueprint $table) {
            $table->id();
			$table->string('model');
			$table->integer('total_data');
			$table->integer('total_imported');
			$table->dateTime('start_time');
			$table->dateTime('end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_data_logs');
    }
}
