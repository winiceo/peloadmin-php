<?php

declare(strict_types=1);



namespace SlimKit\PlusFeed\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Leven\Models\Role as RoleModel;
use Leven\Models\User as UserModel;
use Leven\Models\Ability as AbilityModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SendImageAndTextFeedTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Create the test need user.
     *
     * @return \Leven\Models\User
     */
    protected function createUser(): UserModel
    {
        $user = factory(UserModel::class)->create();
        $ability = AbilityModel::where('name', 'feed-post')->firstOr(function () {
            return factory(AbilityModel::class)->create([
                'name' => 'feed-post',
            ]);
        });
        $role = factory(RoleModel::class)
            ->create([
                'name' => 'test',
            ]);
        $role
            ->abilities()
            ->sync($ability);
        $user->roles()->sync($role);

        return $user;
    }

    /**
     * 获取图片id.
     *
     * @param $user
     * @return array
     */
    private function getFileId($user)
    {
        $file = $this
            ->actingAs($user, 'api')
            ->json('POST', '/api/v2/files', [
                    'file' => UploadedFile::fake()->image('test.jpg')->size(0.00),
                ]
            )
            ->decodeResponseJson();

        return $file['id'];
    }

    /**
     * Test send a image feed.
     *
     * @return void
     */
    public function testSendPublic()
    {
        $user = $this->createUser();
        $response = $this
            ->actingAs($user, 'api')
            ->json('POST', '/api/v2/feeds', [
                'feed_content' => 'test',
                'feed_from' => 5,
                'feed_mark' => intval(time().rand(1000, 9999)),
                'images' => [
                    ['id' => $this->getFileId($user)],
                ],
            ]);
        $response
            ->assertStatus(201)
            ->assertJsonStructure(['id', 'message']);
    }

    /**
     * Test not send ability user send feed.
     *
     * @return void
     */
    public function testSendNotSendAbility()
    {
        $user = factory(UserModel::class)->create();

        $response = $this
            ->actingAs($user, 'api')
            ->json('POST', '/api/v2/feeds', []);

        $response->assertStatus(403);
    }
}
