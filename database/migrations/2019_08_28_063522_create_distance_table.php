<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistanceTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('distance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('start_latitude');
            $table->string('start_longitude');
            $table->string('end_latitude');
            $table->string('end_longitude');
            $table->integer('distance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('distance');
    }
}
