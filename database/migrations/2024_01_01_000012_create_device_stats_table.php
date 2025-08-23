<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceStatsTable extends Migration
{
    public function up()
    {
        Schema::create('device_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->integer('online_count')->default(0);
            $table->integer('offline_count')->default(0);
            $table->dateTime('last_online')->nullable();
            $table->dateTime('last_offline')->nullable();
            $table->integer('current_uptime')->nullable();
            $table->timestamps();

            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_stats');
    }
}
