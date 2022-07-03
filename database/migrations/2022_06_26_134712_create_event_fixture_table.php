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
        Schema::create('event_fixture', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->constrained();
            $table->foreignId('fixture_id')->constrained();
            $table->foreignId('player_id')->constrained();
            $table->unsignedTinyInteger('total');
            $table->timestamps();
            $table->unique(['event_id', 'fixture_id', 'player_id']);
            $table->index('fixture_id');
            $table->index('player_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_fixture');
    }
};
