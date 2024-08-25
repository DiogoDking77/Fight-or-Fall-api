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
        Schema::create('formats', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNullable();
            $table->integer('n_participants')->notNullable();
            $table->integer('n_groups')->nullable();
            $table->integer('n_groups_w')->nullable();
            $table->integer('pts_w')->nullable();
            $table->integer('pts_d')->nullable();
            $table->integer('pts_l')->nullable();
            $table->text('tie_breaker_rule')->nullable();
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
        Schema::dropIfExists('formats');
    }
};
