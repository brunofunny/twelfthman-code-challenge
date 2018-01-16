<?php

namespace App\Api\Controllers;

use Illuminate\Http\Request,
	App\Http\Controllers\Controller,
	App\Image;

class ImagesController extends Controller
{

	/**
	 * List all images from server
	 * @param String $filter
	 * @return Json
	 */
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

	/**
	 * Delete an image from server
	 * @param Integer @id
	 * @return String
	 */
	public function delete($id) {
		$images = Image::find($id);
		$images->deleted = true;
		if(!$images->save()) {
			return response($id, 500);
		}
		return response($id);
	}

	/**
	 * Restore an image from server
	 * @param Integer @id
	 * @return String
	 */
	public function restore($id) {
		$images = Image::find($id);
		$images->deleted = false;
		if(!$images->save()) {
			return response($id, 500);
		}

		return response($id);
	}

	/**
	 * Download an image from the server
	 * @param Integer @id
	 * @return Blob
	 */
	public function download($id) {
		$images = Image::find($id);
		$path = config('custom.images.destinationPath'). $images->file_system_name;
		return response()->download($path, $images->file_original_name);
	}

}