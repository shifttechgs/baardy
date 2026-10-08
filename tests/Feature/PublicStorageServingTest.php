<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Uploads live in storage/app/public. Where the public/storage link is
 * missing (some shared hosts), the app serves them itself at /storage/....
 */
class PublicStorageServingTest extends TestCase
{
    public function test_an_uploaded_image_is_served_without_the_storage_link(): void
    {
        Storage::fake('public');
        Storage::disk('public')->putFileAs('promotions', UploadedFile::fake()->image('a.jpg'), 'a.jpg');

        $this->get('/storage/promotions/a.jpg')->assertOk();
    }

    public function test_a_missing_file_is_not_found(): void
    {
        Storage::fake('public');

        $this->get('/storage/promotions/missing.jpg')->assertNotFound();
    }

    public function test_the_private_disk_is_not_served(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('secret.txt', 'private');

        $this->get('/storage/secret.txt')->assertNotFound();
    }
}
