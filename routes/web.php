<?php

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

Route::get('/covid19', function () {
    return view('covid19.by_total_cases');
});
Route::get('/covid19/by-deaths', function () {
    return view('covid19.by_deaths');
});

Route::get('/covid19/covid.json', 'CovidController@dataJson');
