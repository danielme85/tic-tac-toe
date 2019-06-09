<?php

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

Route::get('/', 'TicTacToeController@index');
Route::get('/api/checkforcurrentplayer', 'TicTacToeController@checkForCurrentPlayer');
Route::post('/api/newplayer', 'TicTacToeController@newPlayer');
Route::post('/api/newgame', 'TicTacToeController@newGame');
Route::post('/api/setplayermove', 'TicTacToeController@setPlayerMove');
Route::post('/api/getcomputermove', 'TicTacToeController@setPlayerMove');

