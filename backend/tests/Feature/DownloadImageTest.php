<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class DownloadImageTest extends TestCase
{

    public $dummy_file = 'dummy_file.jpg';

    /**
     * Runs before each test.
     */
    public function setUp()
    {
        parent::setUp();
        Storage::disk('images')->put($this->dummy_file, $this->dummy_file);
    }

    /**
     * Runs after each test.
     */
    public function tearDown()
    {
        parent::tearDown();
        Storage::disk('images')->delete($this->dummy_file);
    }

    /**
     * Download an image from server.
     *
     * @return void
     */
    public function testDownloadAnImage()
    {
        $dummyFile = 'dummy_file.jpg';
        $image = factory(\App\Image::class)->create([
            'file_system_name' => $dummyFile
        ]);

        $response = $this->get('api/images/download/' . $image->id);

        $response->assertStatus(200);
    }
}
