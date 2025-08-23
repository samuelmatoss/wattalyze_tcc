<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnergyConsumptionsTable extends Migration
{
    public function up()
    {
        Schema::create('energy_consumptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->timestamp('timestamp');
            $table->float('consumption_kwh');
            $table->float('instantaneous_power')->nullable();
            $table->float('voltage')->nullable();
            $table->float('current')->nullable();
            $table->float('power_factor')->nullable();
            $table->float('frequency')->nullable();
            $table->float('temperature')->nullable();
            $table->float('humidity')->nullable();
            $table->boolean('is_peak_hour')->default(false);
            $table->float('cost_estimate')->nullable();
            $table->float('quality_score')->nullable();
            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('energy_consumptions');
    }
}
