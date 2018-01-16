<?php

Route::get('images/{filter?}', '\App\Api\Controllers\ImagesController@list');
Route::delete('images/{id?}', '\App\Api\Controllers\ImagesController@delete');
Route::get('images/restore/{id?}', '\App\Api\Controllers\ImagesController@restore');
Route::get('images/download/{id?}', '\App\Api\Controllers\ImagesController@download');
