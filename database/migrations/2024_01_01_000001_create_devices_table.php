<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mac_address')->unique();
            $table->string('serial_number')->nullable();
            $table->string('model')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('status')->default('offline');
            $table->string('location')->nullable();
            $table->date('installation_date')->nullable();
            $table->float('rated_power')->nullable();
            $table->float('rated_voltage')->nullable();
            $table->unsignedBigInteger('device_type_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('environment_id')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->string('api_token', 64)->nullable()->unique();
            $table->timestamps();

            $table->foreign('device_type_id')->references('id')->on('device_types')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('environment_id')->references('id')->on('environments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices');
    }
}
