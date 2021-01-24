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

Auth::routes();

Route::get('/', 'UploaderController@index')->name('home');
Route::get('/signer/{id}', 'SignerController@index')->name('signer');
Route::get('/uploader', 'UploaderController@index')->name('uploader');
