<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class ImageModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Save an image record test
     *
     * @return void
     */
    public function testSaveImage()
    {

        $image = factory(\App\Image::class)->create();

        $this->assertDatabaseHas('images', $image->toArray());
    }

    /**
     * Delete an image record test
     *
     * @return void
     */
    public function testDeleteImage()
    {

        $image = factory(\App\Image::class)->create();
        $imageId = $image->id;
        $image->delete();

        $this->assertDatabaseMissing('images', [
            'id' => $imageId
        ]);
    }

    /**
     * Update an image record test
     *
     * @return void
     */
    public function testUpdateImage()
    {

        $image = factory(\App\Image::class)->create();
        $originalNameBkp = $image->file_original_name;
        $image->file_original_name = 'a_new_name.sys';
        $image->save();

        $this->assertDatabaseMissing('images', [
            'file_original_name' => $originalNameBkp
        ]);
    }
}
