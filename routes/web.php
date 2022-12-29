<?php

use App\Http\Controllers\FixtureController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'fixtures'], function () {
    Route::get('calendar', [FixtureController::class, 'calendar']);
    Route::get('{league}', [LeagueController::class, 'fixtures']);
});

Route::group(['prefix' => 'player-stats', 'controller' => PlayerController::class], function () {
    Route::get('', 'index');
    Route::prefix('{league}')->group(function () {
        Route::get('', 'index');
        Route::prefix('{team}')->group(function () {
            Route::get('', 'PlayerStatController@showTeam');
            Route::get('form', 'PlayerStatController@showTeamForm');
            Route::get('{player:name}', [PlayerController::class, 'show']);
        });
    });
});
