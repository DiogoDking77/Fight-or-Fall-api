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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_participant_a')->constrained('participants')->onDelete('cascade');
            $table->foreignId('id_participant_b')->constrained('participants')->onDelete('cascade');
            $table->integer('score_a')->nullable();
            $table->integer('score_b')->nullable();
            $table->foreignId('id_next_game_w')->nullable()->constrained('games');
            $table->foreignId('id_next_game_l')->nullable()->constrained('games');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
};
