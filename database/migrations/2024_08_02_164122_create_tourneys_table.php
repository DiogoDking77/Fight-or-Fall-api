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
        Schema::create('tourneys', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNullable();
            $table->text('description')->nullable();
            $table->foreignId('user_creator_id')->constrained('users')->onDelete('cascade');
            $table->string('theme_name')->notNullable();
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
        Schema::dropIfExists('tourneys');
    }
};
