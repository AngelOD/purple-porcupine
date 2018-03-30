<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRadioDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('radio_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('radio_bus_id');
            $table->integer('channel');
            $table->string('node_mac_address');
            $table->integer('packet_type');
            $table->integer('sequence_number');
            $table->string('timestamp');
            $table->string('timestamp_tz');
            $table->integer('v_bat');
            $table->integer('vcc');
            $table->integer('temperature');
            $table->integer('humidity');
            $table->integer('pressure');
            $table->integer('co2');
            $table->integer('tvoc');
            $table->integer('light');
            $table->integer('uv');
            $table->integer('sound_pressure');
            $table->integer('port_input');
            $table->string('mag');
            $table->string('acc');
            $table->string('gyro');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('radio_datas');
    }
}
