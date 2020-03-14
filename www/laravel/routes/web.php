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
// TODO: アロー関数だとパースエラーが発生: phpは7.4に設定済
// Route::get('/{any?}', fn() => view('index'))->where('any', '.+');
Route::get('/{any?}', function() { return view('index'); })->where('any', '.+');