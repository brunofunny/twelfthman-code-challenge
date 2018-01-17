<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteTest extends TestCase
{
    /**
     * List all images from server.
     *
     * @return void
     */
    public function testListAllImages()
    {
        $image = factory(\App\Image::class)->create();
        
        $response = $this->get('api/images/all');
        $response->assertStatus(200);
    }

    /**
     * List all deleted images from server.
     *
     * @return void
     */
    public function testListAllDeletedImages()
    {

        $image = factory(\App\Image::class)->create();

        $response = $this->get('api/images/deleted');
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertStatus(200);
    }

    /**
     * Restore an image from server.
     *
     * @return void
     */
    public function testRestoreImage()
    {
        $image = factory(\App\Image::class)->create();

        $response = $this->get('api/images/restore/' . $image->id);
        $response->assertStatus(200);
    }

    /**
     * Delete an image from server.
     *
     * @return void
     */
    public function testDeleteAnImage()
    {
        $image = factory(\App\Image::class)->create();

        $response = $this->delete('api/images/' . $image->id);
        $response->assertStatus(200);
    }

}
