<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'HotOrNot\VotingController@index');
Route::post('vote', 'HotOrNot\VotingController@vote');
Route::get('add-user', 'HotOrNot\SubjectController@add');
Route::post('users', 'HotOrNot\SubjectController@post');
Route::get('statistics', 'HotOrNot\ReportingController@index');
