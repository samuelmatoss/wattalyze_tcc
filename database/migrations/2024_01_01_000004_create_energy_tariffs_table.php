<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnergyTariffsTable extends Migration
{
    public function up()
    {
        Schema::create('energy_tariffs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable(false);

            $table->string('name');
            $table->string('provider')->nullable();
            $table->string('region')->nullable();
            $table->string('tariff_type')->nullable();

            // Faixas progressivas de consumo — exemplo 3 faixas:
            $table->decimal('bracket1_min', 10, 2)->default(0);
            $table->decimal('bracket1_max', 10, 2)->nullable();
            $table->decimal('bracket1_rate', 10, 4);

            $table->decimal('bracket2_min', 10, 2)->nullable();
            $table->decimal('bracket2_max', 10, 2)->nullable();
            $table->decimal('bracket2_rate', 10, 4)->nullable();

            $table->decimal('bracket3_min', 10, 2)->nullable();
            $table->decimal('bracket3_max', 10, 2)->nullable();
            $table->decimal('bracket3_rate', 10, 4)->nullable();

            $table->decimal('tax_rate', 5, 2)->nullable();

            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Foreign key para usuário
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('energy_tariffs');
    }
}
