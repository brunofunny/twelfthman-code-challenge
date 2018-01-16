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
        $response = $this->get('api/images/deleted');

        $response->assertStatus(200);
    }

    /**
     * Restore an image from server.
     *
     * @return void
     */
    public function testRestoreImage()
    {
        $response = $this->get('api/images/restore/000');

        $response->assertStatus(500);
    }

    /**
     * Delete an image from server.
     *
     * @return void
     */
    public function testDeleteAnImage()
    {
        $response = $this->get('api/images/download/000');

        $response->assertStatus(500);
    }
}
