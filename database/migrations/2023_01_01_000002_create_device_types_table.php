<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDeviceTypesTable extends Migration
{
   public function up()
    {
        Schema::create('device_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Inserir os device types iniciais
        DB::table('device_types')->insert([
            ['name' => 'Energy Meter', 'description' => 'Medidor de energia elétrica', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Temperature Sensor', 'description' => 'Sensor de temperatura', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Humidity Sensor', 'description' => 'Sensor de umidade', 'created_at' => now(), 'updated_at' => now()],

        ]);
    }

    public function down()
    {
        Schema::dropIfExists('device_types');
    }
}
