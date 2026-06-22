<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── sensores ─────────────────────────────────────────────────────────
        Schema::create('sensores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo', 20)->unique();
            $table->string('nome', 100);
            $table->unsignedInteger('endereco_id')->nullable();
            $table->unsignedInteger('bairro_id')->nullable();
            $table->decimal('latitude',  10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->foreign('endereco_id')->references('id')->on('enderecos')->nullOnDelete();
            $table->foreign('bairro_id')->references('id')->on('bairros')->nullOnDelete();
        });

        // ── leituras ──────────────────────────────────────────────────────────
        Schema::create('leituras', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sensor_id');
            $table->decimal('obstrucao_pct',    5, 2);
            $table->decimal('precipitacao_mm',  7, 3);
            $table->decimal('vazao_lps',        9, 3);
            $table->timestamp('registrado_em')->useCurrent();

            $table->foreign('sensor_id')->references('id')->on('sensores')->onDelete('cascade');
            $table->index(['sensor_id', 'registrado_em']);
        });

        // ── alertas ───────────────────────────────────────────────────────────
        Schema::create('alertas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sensor_id');
            $table->string('severidade', 20);
            $table->text('mensagem');
            $table->timestamp('resolvido_em')->nullable();
            $table->timestamps();

            $table->foreign('sensor_id')->references('id')->on('sensores')->onDelete('cascade');
            $table->index(['sensor_id', 'resolvido_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
        Schema::dropIfExists('leituras');
        Schema::dropIfExists('sensores');
    }
};
