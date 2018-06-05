<?php

declare(strict_types=1);



namespace Leven\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Leven\Models\File as FileModel;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileUploadedTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * The test user.
     *
     * @var Leven\Models\User
     */
    protected $user;

    /**
     * The test set up fearure.
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(UserModel::class)->create();
    }

    /**
     * Test not uploaded file hash check.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testNotHash()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');
        $hash = md5_file((string) $file);
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/v2/files/uploaded/'.$hash);

        $response->assertStatus(404);
    }

    /**
     * Test Uploaded file hash.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function testUsedHash()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');
        $hash = md5_file((string) $file);

        factory(FileModel::class)->create([
            'hash' => $hash,
            'origin_filename' => 'test.jpg',
            'filename' => 'test.jpg',
            'mime' => 'image/jpeg',
            'width' => '0.00',
            'height' => '0.00',
        ]);
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/v2/files/uploaded/'.$hash);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'id']);
    }
}
