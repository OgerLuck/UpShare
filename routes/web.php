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

Route::get('/', function () {
    return view('upload-form');
});
Route::get('/{link}', 'MainProcess@viewFile');
Route::get('/download/{one_time_link}/{link}', 'MainProcess@downloadFile');

Route::post('/p/process-background', 'MainProcess@processBackground');
Route::post('/p/upload-file', 'MainProcess@uploadFile');
Route::post('/p/download-file', 'MainProcess@createOneTimeLink');

//Route::post('/get-upload-url', 'MainProcessForJS@uploadFile');

