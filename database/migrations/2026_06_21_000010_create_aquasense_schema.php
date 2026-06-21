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
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nome', 100);
            $table->string('endereco', 200);
            $table->foreignId('bairro_id')->nullable()->constrained('bairros')->nullOnDelete();
            $table->decimal('latitude',  10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        // ── leituras ──────────────────────────────────────────────────────────
        Schema::create('leituras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')
                  ->constrained('sensores')
                  ->onDelete('cascade');

            $table->decimal('obstrucao_pct',    5, 2);
            $table->decimal('precipitacao_mm',  7, 3);
            $table->decimal('vazao_lps',        9, 3);

            $table->timestamp('registrado_em')->useCurrent();

            $table->index(['sensor_id', 'registrado_em']);
        });

        // ── alertas ───────────────────────────────────────────────────────────
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')
                  ->constrained('sensores')
                  ->onDelete('cascade');

            $table->string('severidade', 20); // atencao | risco | critico
            $table->text('mensagem');
            $table->timestamp('resolvido_em')->nullable();
            $table->timestamps();

            $table->index(['sensor_id', 'resolvido_em']);
        });

        // ── manutencoes ───────────────────────────────────────────────────────
        Schema::create('manutencoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')
                  ->constrained('sensores')
                  ->onDelete('cascade');

            $table->string('operador', 100);
            $table->text('descricao');
            $table->text('observacoes')->nullable();
            $table->timestamp('realizado_em');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manutencoes');
        Schema::dropIfExists('alertas');
        Schema::dropIfExists('leituras');
        Schema::dropIfExists('sensores');
    }
};
