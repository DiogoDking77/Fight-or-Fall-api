<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Adicionar chave estrangeira para a tabela matches
        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('phase_id')->constrained('phases')->onDelete('cascade');
        });

        // Adicionar chave estrangeira para a tabela bracket
        Schema::table('bracket', function (Blueprint $table) {
            $table->foreignId('phase_id')->constrained('phases')->onDelete('cascade');
        });

        // Adicionar chave estrangeira para a tabela groups
        Schema::table('groups', function (Blueprint $table) {
            $table->foreignId('phase_id')->constrained('phases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remover a chave estrangeira da tabela matches
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['phase_id']);
        });

        // Remover a chave estrangeira da tabela bracket
        Schema::table('bracket', function (Blueprint $table) {
            $table->dropForeign(['phase_id']);
        });

        // Remover a chave estrangeira da tabela groups
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['phase_id']);
        });
    }
};
