<?php

namespace Twelfthman\Api\Library\Controllers;

use Illuminate\Http\Request,
	Twelfthman\Sys\Http\Controllers\Controller,
	Twelfthman\Support\Helpers,
	GuzzleHttp\Client,
	Validator;

class LibraryController extends Controller
{

	public function __construct() {
	}

	public function test($filter) {

		if ($filter === "all") {
			$images = [
				[
					'id' => 1,
					'name' => 'Partnership Rationale',
					'image' => 'image-1.jpg',
					'deleted' => false
				],
				[
					'id' => 2,
					'name' => 'Partnership Rationale',
					'image' => 'image-2.jpg',
					'deleted' => false
				],
				[
					'id' => 3,
					'name' => 'Partnership Rationale',
					'image' => 'image-3.jpg',
					'deleted' => false
				],
				[
					'id' => 4,
					'name' => 'Partnership Rationale',
					'image' => 'image-4.jpg',
					'deleted' => false
				],
				[
					'id' => 5,
					'name' => 'Partnership Rationale',
					'image' => 'image-5.jpg',
					'deleted' => false
				],
			];
		} else if ($filter === "deleted") {
			$images = [
				[
					'id' => 6,
					'name' => 'Partnership Rationale',
					'image' => 'image-1.jpg',
					'deleted' => true
				],
				[
					'id' => 7,
					'name' => 'Partnership Rationale',
					'image' => 'image-2.jpg',
					'deleted' => true
				],
				[
					'id' => 8,
					'name' => 'Partnership Rationale',
					'image' => 'image-3.jpg',
					'deleted' => true
				]
			];
		} else {
			$images = [];
		}

		return response()->json($images);
	}

	public function delete($id) {
		return response($id);
	}

	public function restore($id) {
		return response($id);
	}

}