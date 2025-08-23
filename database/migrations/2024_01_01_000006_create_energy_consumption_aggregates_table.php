<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnergyConsumptionAggregatesTable extends Migration
{
    public function up()
    {
        Schema::create('energy_consumption_aggregates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->string('period_type');
            $table->dateTime('period_start');
            $table->dateTime('period_end')->nullable();
            $table->float('total_consumption_kwh')->nullable();
            $table->float('avg_power')->nullable();
            $table->float('max_power')->nullable();
            $table->float('min_power')->nullable();
            $table->float('total_cost')->nullable();
            $table->float('peak_consumption_kwh')->nullable();
            $table->float('off_peak_consumption_kwh')->nullable();
            $table->integer('data_points_count')->nullable();
            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('energy_consumption_aggregates');
    }
}
