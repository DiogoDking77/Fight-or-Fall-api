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
        Schema::create('editions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('edition_order')->notNullable();
            $table->date('start_date')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->integer('n_participants')->notNullable();
            $table->integer('current_phase')->notNullable();
            $table->foreignId('tourney_id')->constrained('tourneys')->onDelete('cascade');
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
        Schema::dropIfExists('editions');
    }
};
