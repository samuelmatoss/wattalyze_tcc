<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnvironmentsTable extends Migration
{
    public function up()
    {
        Schema::create('environments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->float('size_sqm')->nullable();
            $table->integer('occupancy')->nullable();
            $table->string('voltage_standard')->nullable();
            $table->string('tariff_type')->nullable();
            $table->string('energy_provider')->nullable();
            $table->date('installation_date')->nullable();
            $table->boolean('is_default')->default(false);
            $table->json('settings')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('environments');
    }
}
