<?php

declare(strict_types=1);



namespace SlimKit\PlusFeed\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\Role as RoleModel;
use Leven\Models\User as UserModel;
use Leven\Models\Ability as AbilityModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteFeedCommentTest extends TestCase
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
        $role = RoleModel::where('name', 'test')->firstOr(function () {
            return factory(RoleModel::class)->create([
                'name' => 'test',
            ]);
        });
        $role
            ->abilities()
            ->sync($ability);
        $user->roles()->sync($role);

        return $user;
    }

    /**
     * 添加测试动态.
     *
     * @param $user
     * @return mixed
     */
    protected function addFeed($user)
    {
        $response = $this->actingAs($user, 'api')
            ->json('POST', '/api/v2/feeds', [
                'feed_content' => 'test',
                'feed_from' => 5,
                'feed_mark' => intval(time().rand(1000, 9999)),
            ])
            ->decodeResponseJson();

        return $response['id'];
    }

    /**
     * 添加动态评论测试数据.
     *
     * @param $user
     * @param $feed
     * @return mixed
     * @throws \Exception
     */
    protected function addFeedComment($user, $feed)
    {
        $response = $this
            ->actingAs($user, 'api')
            ->json('POST', "/api/v2/feeds/{$feed}/comments", [
                'body' => 'test',
            ])
            ->decodeResponseJson();

        return $response['comment']['id'];
    }

    /**
     * 删除动态评论.
     *
     * @return mixed
     * @throws \Exception
     */
    public function testDeleteFeedComment()
    {
        $user = $this->createUser();
        $feed = $this->addFeed($user);
        $comm = $this->addFeedComment($user, $feed);

        $response = $this
            ->json('DELETE', "/api/v2/feeds/{$feed}/comments/{$comm}");
        $response
            ->assertStatus(204);
    }
}
