<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('id_participant1')->nullable()->constrained('participants')->onDelete('cascade');
            $table->foreignId('id_participant2')->nullable()->constrained('participants')->onDelete('cascade');
            $table->foreignId('id_winner')->nullable()->constrained('participants')->onDelete('cascade');
            $table->foreignId('id_loser')->nullable()->constrained('participants')->onDelete('cascade');
            $table->foreignId('id_group')->nullable()->constrained('groups')->onDelete('cascade');
            $table->foreignId('id_bracket')->nullable()->constrained('bracket')->onDelete('cascade');
        });

        Schema::table('bracket', function (Blueprint $table) {
            $table->foreignId('winner_match_id')->nullable()->constrained('matches')->onDelete('cascade');
            $table->foreignId('loser_match_id')->nullable()->constrained('matches')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['id_bracket']);
            $table->dropColumn('id_bracket');
        });

        Schema::table('bracket', function (Blueprint $table) {
            $table->dropForeign(['winner_match_id']);
            $table->dropForeign(['loser_match_id']);
            $table->dropColumn('winner_match_id');
            $table->dropColumn('loser_match_id');
        });
    }
};

