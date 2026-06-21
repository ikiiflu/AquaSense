<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_consultas', function (Blueprint $table) {
            $table->id();
            $table->text('sql_query');
            $table->json('bindings')->nullable();
            $table->float('tempo_ms')->default(0);
            $table->timestamp('executado_em')->useCurrent();
            $table->index('executado_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_consultas');
    }
};
