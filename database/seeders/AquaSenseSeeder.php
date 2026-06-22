<?php

namespace Database\Seeders;

use App\Models\Bairro;
use App\Models\Endereco;
use App\Models\Sensor;
use Illuminate\Database\Seeder;

class AquaSenseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Bairros ────────────────────────────────────────────────────────────
        $bairros = [
            'Centro',
            'Limoeiro',
            'Santa Zita',
            'Esplanada',
            'Zacarias',
        ];

        foreach ($bairros as $nome) {
            Bairro::firstOrCreate(['nome' => $nome]);
        }

        // ── Endereços ──────────────────────────────────────────────────────────
        $enderecos = [
            'Av. Olegário Maciel',
            'Av. Catarina Cimini',
            'Av. Ana Pena de Faria',
            'Rua do Santuário',
            'Rua Manoel Gonçalves de Castro',
            'Rua Raimundo Cimini',
            'Praça Cesário Alvim',
        ];

        foreach ($enderecos as $logradouro) {
            Endereco::firstOrCreate(['logradouro' => $logradouro]);
        }

        // ── Carregar referências por nome ──────────────────────────────────────
        $centro    = Bairro::where('nome', 'Centro')->first();
        $limoeiro  = Bairro::where('nome', 'Limoeiro')->first();
        $santaZita = Bairro::where('nome', 'Santa Zita')->first();
        $esplanada = Bairro::where('nome', 'Esplanada')->first();
        $zacarias  = Bairro::where('nome', 'Zacarias')->first();

        $avOlegario   = Endereco::where('logradouro', 'Av. Olegário Maciel')->first();
        $avCatarina   = Endereco::where('logradouro', 'Av. Catarina Cimini')->first();
        $avAnaPena    = Endereco::where('logradouro', 'Av. Ana Pena de Faria')->first();
        $ruaSantuario = Endereco::where('logradouro', 'Rua do Santuário')->first();
        $ruaManoel    = Endereco::where('logradouro', 'Rua Manoel Gonçalves de Castro')->first();
        $ruaRaimundo  = Endereco::where('logradouro', 'Rua Raimundo Cimini')->first();
        $pracaCesario = Endereco::where('logradouro', 'Praça Cesário Alvim')->first();

        // ── Sensores ───────────────────────────────────────────────────────────
        $sensors = [
            [
                'codigo'      => '1',
                'nome'        => 'Olegário Maciel 1',
                'bairro_id'   => $centro?->id,
                'endereco_id' => $avOlegario?->id,
                'latitude'    => -19.7877681,
                'longitude'   => -42.1418824,
            ],
            [
                'codigo'      => '2',
                'nome'        => 'Olegário Maciel 2',
                'bairro_id'   => $centro?->id,
                'endereco_id' => $avOlegario?->id,
                'latitude'    => -19.7848159,
                'longitude'   => -42.1416676,
            ],
            [
                'codigo'      => '3',
                'nome'        => 'Catarina Cimini 1',
                'bairro_id'   => $centro?->id,
                'endereco_id' => $avCatarina?->id,
                'latitude'    => -19.7882948,
                'longitude'   => -42.1396478,
            ],
            [
                'codigo'      => '4',
                'nome'        => 'Olegário Maciel 3',
                'bairro_id'   => $centro?->id,
                'endereco_id' => $avOlegario?->id,
                'latitude'    => -19.7830666,
                'longitude'   => -42.1406284,
            ],
            [
                'codigo'      => '5',
                'nome'        => 'Limoeiro 1',
                'bairro_id'   => $limoeiro?->id,
                'endereco_id' => $avAnaPena?->id,
                'latitude'    => -19.7979610,
                'longitude'   => -42.1388059,
            ],
            [
                'codigo'      => '6',
                'nome'        => 'Limoeiro 2',
                'bairro_id'   => $limoeiro?->id,
                'endereco_id' => $avAnaPena?->id,
                'latitude'    => -19.7972559,
                'longitude'   => -42.1415139,
            ],
            [
                'codigo'      => '7',
                'nome'        => 'Santuário 1',
                'bairro_id'   => $santaZita?->id,
                'endereco_id' => $ruaSantuario?->id,
                'latitude'    => -19.7909275,
                'longitude'   => -42.1341940,
            ],
            [
                'codigo'      => '8',
                'nome'        => 'Esplanada 1',
                'bairro_id'   => $esplanada?->id,
                'endereco_id' => $ruaManoel?->id,
                'latitude'    => -19.7791027,
                'longitude'   => -42.1308492,
            ],
            [
                'codigo'      => '9',
                'nome'        => 'Zacarias 1',
                'bairro_id'   => $zacarias?->id,
                'endereco_id' => $ruaRaimundo?->id,
                'latitude'    => -19.7695808,
                'longitude'   => -42.1316359,
            ],
            [
                'codigo'      => '10',
                'nome'        => 'Praça 1',
                'bairro_id'   => $centro?->id,
                'endereco_id' => $pracaCesario?->id,
                'latitude'    => -19.7904016,
                'longitude'   => -42.1404759,
            ],
        ];

        foreach ($sensors as $data) {
            Sensor::firstOrCreate(
                ['codigo' => $data['codigo']],
                array_merge($data, ['ativo' => true])
            );
        }
    }
}
