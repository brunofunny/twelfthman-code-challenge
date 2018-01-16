<?php

// List Images According to filter
Route::get('images/{filter?}', '\App\Api\Controllers\ImagesController@list');
// Mark an image as deleted
Route::delete('images/{id}', '\App\Api\Controllers\ImagesController@delete');
// Restore an image
Route::get('images/restore/{id}', '\App\Api\Controllers\ImagesController@restore');
// Download imagem from server
Route::get('images/download/{id}', '\App\Api\Controllers\ImagesController@download');
