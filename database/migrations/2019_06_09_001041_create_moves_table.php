<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('player_id')->unsigned();
            $table->string('move');
            $table->timestamps();
        });

        Schema::create('game_moves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('game_id')->unsigned();
            $table->bigInteger('move_id')->unsigned();
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
        Schema::dropIfExists('moves');
        Schema::dropIfExists('game_moves');
    }
}
