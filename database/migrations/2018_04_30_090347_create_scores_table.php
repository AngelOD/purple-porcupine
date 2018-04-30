<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('timestamp');
            $table->integer('room_id');
            $table->double('total_score');
            $table->double('IAQ_score');
            $table->double('temperature_humidity_score');
            $table->double('sound_score');
            $table->double('visual_score');
            $table->double('voc_score');
            $table->double('temperature_score');
            $table->double('humidity_score');
            $table->double('light_score');
            $table->double('uv_score');
            $table->double('co2_score');
            $table->double('noise_score');

            // Foreign key.
            $table->foreign('room_id')->references('id')->on('rooms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scores');
    }
}
