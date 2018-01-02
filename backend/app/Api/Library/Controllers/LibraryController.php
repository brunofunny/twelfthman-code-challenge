<?php

namespace Twelfthman\Api\Library\Controllers;

use Illuminate\Http\Request,
	Twelfthman\Sys\Http\Controllers\Controller,
	Twelfthman\Support\Helpers,
	GuzzleHttp\Client,
	Validator;

class LibraryController extends Controller
{
	
	public $api = 'https://bibliapp.herokuapp.com/api/';
	public $valid_mime = ['text/csv', 'text/txt', 'text/plain'];
	public $valid_ext = ['txt', 'csv'];
	public $client;
	public $helpers;

	public function __construct() {
		$this->client = new Client(['base_uri' => $this->api]);
		$this->helpers = new Helpers;
	}

	/**
	 * Main import function
	 * @param  Request $request
	 * @return String
	 */
	public function import(Request $request)
	{

		try {

			$validate = Validator::make($request->all(), [
				'file' => 'required',
			]);
			if ($validate->fails()) {
				return response()->json($validate->errors(), 400);
			}

			$exp = explode(',', $request->file);
			if (!in_array($this->helpers->getMimeFromBase64($exp[1]), $this->valid_mime)) {
				return response()->json([
					'file' => ['Only the following extensions are supported: ' . implode(', ', $this->valid_ext)]
				], 400);
			}

			$csv = array_map('str_getcsv', explode("\n", base64_decode($exp[1])));

			$this->saveToApi($this->loadCSV($csv));

		} catch (\Throwable $e) {
			$error = $e->getMessage();
			return response()->json([
					'file' => ['File is corrupted or it doesn\'t follow the template requested.']
				], 400);
		} catch (\Exception $e) {
			$error = $e->getMessage();
			return response()->json(['error' => $error], 500);
		}

		return response()->json([], 200);

	}

	/**
	 * Save data to API
	 *
	 * @param array $data
	 */
	public function saveToApi(Array $data)
	{

		foreach ($data as $author => $authorData) {
			$dataAuthor = [
				'firstName' => $authorData['firstName'], 
				'lastName' => $authorData['lastName']
			];

			$saveAuthorUrl = 'authors/upsertWithWhere?where=' . $this->helpers->jsonToUrl($dataAuthor);
			$saveAuthor = $this->client->request('POST', $saveAuthorUrl, [
				'json' => $dataAuthor
			]);
			if ($saveAuthor->getStatusCode() === 200) {
				$response = $this->helpers->apiGetContents($saveAuthor);

				foreach ($authorData['books'] as $book) {
					$dataBook = [
						'title' => $book, 
						'authorId' => $response->id
					];
					$saveBookUrl = 'books/upsertWithWhere?where=' . $this->helpers->jsonToUrl($dataBook);
					$saveBook = $this->client->request('POST', $saveBookUrl, [
						'json' => $dataBook
					]);
					if ($saveBook->getStatusCode() !== 200) {
						throw \Exception('An error occourred during the creation of a book');
					}
				}
			} else {
				throw \Exception('An error occourred during the creation of an author');
			}
		}
	}

	/**
	 * Load array converted from a CSV file
	 *
	 * @param String $file
	 * @return array|string
	 */
	public function loadCSV(Array $arr)
	{
		try {
			$authors = [];
			foreach ($arr as $data) {

				$author = trim(htmlspecialchars($this->helpers->slugify($data[1])));
				$book = trim(htmlspecialchars($this->helpers->slugify($data[0])));
				$formattedName = $this->helpers->formatName($author);

				if (!array_key_exists($author, $authors)) {
					$authors[$author] = [
						'id' => null,
						'firstName' => $this->helpers->slugify($formattedName[0]),
						'lastName' => $this->helpers->slugify($formattedName[1]),
						'books' => [
							$book
						]
					];
				} else {
					$authors[$author]['books'][] = $book;
				}
			}
			return $authors;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Fetch all authors
	 *
	 * @return mixed
	 */
	public function fetchAuthors()
	{
		return response()->json($this->helpers->apiGetContents($this->client->request('GET', 'authors')));
	}

	/**
	 * Fetch all books
	 *
	 * @return mixed
	 */
	public function fetchBooks()
	{
		return response()->json($this->helpers->apiGetContents($this->client->request('GET', 'books')));
	}	

}