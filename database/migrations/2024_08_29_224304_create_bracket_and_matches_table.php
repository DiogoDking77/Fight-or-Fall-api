<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->integer('round')->notNullable();
            $table->integer('score1');
            $table->integer('score2');
            
            // Referência para 'id_bracket' removida nesta fase
            $table->timestamps();
        });

        Schema::create('bracket', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_winner_bracket');
            $table->boolean('is_final');
            // Referências para 'winner_match_id' e 'loser_match_id' removidas nesta fase
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('matches');
        Schema::dropIfExists('bracket');
    }
};

