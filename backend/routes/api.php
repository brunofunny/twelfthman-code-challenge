<?php

Route::get('images/{filter?}', '\Twelfthman\Api\Library\Controllers\LibraryController@test');
Route::delete('images/{id?}', '\Twelfthman\Api\Library\Controllers\LibraryController@delete');
Route::get('images-restore/{id?}', '\Twelfthman\Api\Library\Controllers\LibraryController@restore');