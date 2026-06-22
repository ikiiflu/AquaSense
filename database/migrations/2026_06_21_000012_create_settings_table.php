<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('chave', 100)->unique();
            $table->text('valor')->nullable();
            $table->string('rotulo', 200)->nullable();
            $table->timestamps();
        });

        DB::table('configuracoes')->insert([
            ['chave' => 'intervalo_leitura_seg',     'valor' => '60',      'rotulo' => 'Intervalo entre leituras dos sensores (segundos)',      'created_at' => now(), 'updated_at' => now()],
            ['chave' => 'limite_atencao',             'valor' => '10',      'rotulo' => 'Obstrução (%) para alerta Atenção',                     'created_at' => now(), 'updated_at' => now()],
            ['chave' => 'limite_risco',               'valor' => '40',      'rotulo' => 'Obstrução (%) para alerta Risco',                       'created_at' => now(), 'updated_at' => now()],
            ['chave' => 'limite_critico',             'valor' => '70',      'rotulo' => 'Obstrução (%) para alerta Crítico',                     'created_at' => now(), 'updated_at' => now()],
            ['chave' => 'modo_simulacao',             'valor' => 'normal',  'rotulo' => 'Modo de simulação de chuva',                            'created_at' => now(), 'updated_at' => now()],
            ['chave' => 'modo_atualizacao',           'valor' => 'manual',  'rotulo' => 'Modo de atualização da página',                         'created_at' => now(), 'updated_at' => now()],
            ['chave' => 'intervalo_atualizacao_seg',  'valor' => '60',      'rotulo' => 'Intervalo de atualização automática da página (segundos)', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracoes');
    }
};
