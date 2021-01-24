<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/documents', 'DocumentController@index');
Route::get('/documents/{id}', 'DocumentController@get');
Route::post('/documents', 'DocumentController@create');
Route::post('/documents/{id}/sign', 'SignerController@sign');
Route::put('/documents/{id}', 'DocumentController@update');
Route::delete('/documents/{id}', 'DocumentController@delete');

Route::get('/signature_positions/{id}', 'SignaturePositionController@get');
Route::post('/signature_positions', 'SignaturePositionController@create');
Route::put('/signature_positions/{id}', 'SignaturePositionController@update');
