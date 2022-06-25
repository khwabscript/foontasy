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
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->foreignId('league_id')->constrained();
            $table->timestamp('datetime');
            $table->unsignedTinyInteger('tour')->nullable();
            $table->unsignedTinyInteger('fantasy_tour')->nullable();
            $table->unsignedBigInteger('home_team_id');
            $table->foreign('home_team_id')->references('id')->on('teams')->constrained();
            $table->unsignedBigInteger('away_team_id');
            $table->foreign('away_team_id')->references('id')->on('teams')->constrained();
            $table->unsignedTinyInteger('home_team_goals')->nullable();
            $table->unsignedTinyInteger('away_team_goals')->nullable();
            $table->timestamps();
            $table->unique(['external_id', 'league_id']);
            $table->index('external_id');
            $table->index('datetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixtures');
    }
};
