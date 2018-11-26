<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/asset/{tag}', 'AssetsController@findByTag');
Route::get('/history/{userId}', 'AssetsController@getUserHistory');
Route::post('/login', 'UserController@login');
