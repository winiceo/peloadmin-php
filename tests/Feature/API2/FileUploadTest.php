<?php

declare(strict_types=1);



namespace Leven\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileUploadTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * The test user.
     *
     * @var Leven\Models\User
     */
    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(UserModel::class)->create();
    }

    /**
     * 测试上传文件.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testUploadFile()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this
            ->actingAs($this->user, 'api')
            ->json('POST', '/api/v2/files', ['file' => $file]);

        $response->assertStatus(201)->assertJsonStructure(['id', 'message']);
    }

    protected function tearDown()
    {
        $this->user->forceDelete();

        parent::tearDown();
    }
}
