<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertRulesTable extends Migration
{
    public function up()
    {
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('device_id')->nullable();
            $table->unsignedBigInteger('environment_id')->nullable();
            $table->string('name');
            $table->string('type');
            $table->json('condition')->nullable();
            $table->float('threshold_value')->nullable();
            $table->integer('time_window')->nullable();
            $table->json('notification_channels')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('set null');
            $table->foreign('environment_id')->references('id')->on('environments')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('alert_rules');
    }
}
