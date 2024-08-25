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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->notNullable();
            $table->integer('n_games_played')->notNullable();
            $table->integer('points')->nullable();
            $table->integer('n_wins')->nullable();
            $table->integer('n_draws')->nullable();
            $table->integer('n_loses')->nullable();
            $table->integer('n_tie_breaker_atb')->nullable();
            $table->foreignId('id_edition')->constrained('editions')->onDelete('cascade');
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
        Schema::dropIfExists('participants');
    }
};
