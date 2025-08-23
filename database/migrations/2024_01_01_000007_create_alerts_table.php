<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
         Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID do usuário
            $table->unsignedBigInteger('device_id')->nullable(); // ID do dispositivo
            $table->unsignedBigInteger('environment_id')->nullable(); // ID do ambiente
            $table->unsignedBigInteger('alert_rule_id'); // ID da regra de alerta
            $table->string('type'); // Tipo do alerta
            $table->string('severity')->nullable(); // Severidade do alerta
            $table->string('title'); // Título do alerta
            $table->text('message'); // Mensagem do alerta
            $table->float('threshold_value')->nullable(); // Valor de limiar
            $table->float('actual_value')->nullable(); // Valor atual
            $table->boolean('is_resolved')->default(false); // Se o alerta foi resolvido
            $table->boolean('is_read')->default(false); // Se o alerta foi lido
            $table->timestamps(); // Campos de timestamp
            // Chaves estrangeiras
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('set null');
            $table->foreign('environment_id')->references('id')->on('environments')->onDelete('set null');
            $table->foreign('alert_rule_id')->references('id')->on('alert_rules')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
