<?php

namespace App\Api\Controllers;

use Illuminate\Http\Request,
	App\Http\Controllers\Controller,
	App\Image;

class ImagesController extends Controller
{

	public function __construct() {
	}

	public function list($filter) {

		if ($filter === "all") {
			$images = Image::where(['deleted' => false])->get();
		} else if ($filter === "deleted") {
			$images = Image::where(['deleted' => true])->get();
		} else {
			$images = [];
		}

		return response()->json($images);
	}

	public function delete($id) {
		$images = Image::find($id);
		$images->deleted = true;
		$images->save();
		return response($id);
	}

	public function restore($id) {
		$images = Image::find($id);
		$images->deleted = false;
		$images->save();
		return response($id);
	}

	public function download($id) {
		$images = Image::find($id);
		$path = storage_path('../public/imgs/' . $images->file_system_name);
		return response()->download($path, $images->file_original_name);
	}

}