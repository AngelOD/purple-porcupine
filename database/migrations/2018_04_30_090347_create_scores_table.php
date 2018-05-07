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
            $table->bigInteger('end_time');
            $table->integer('interval');
            $table->integer('room_id')->unsigned();
            $table->double('total_score');
            $table->double('IAQ_score');
            $table->double('sound_score');
            $table->double('temp_hum_score');
            $table->double('visual_score');
            $table->timestamps();

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
