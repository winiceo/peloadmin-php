<?php

declare(strict_types=1);



namespace SlimKit\PlusFeed\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\Role as RoleModel;
use Leven\Models\User as UserModel;
use Leven\Models\Ability as AbilityModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GetFeedCommentTest extends TestCase
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
     * 添加测试动态.
     *
     * @param $user
     * @return mixed
     * @throws \Exception
     */
    protected function addFeed($user)
    {
        $response = $this->actingAs($user, 'api')
            ->json('POST', '/api/v1/feeds', [
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
        $response = $this->actingAs($user, 'api')
            ->json('POST', "/api/v1/feeds/{$feed}/comments", [
                'body' => 'test',
            ])
            ->decodeResponseJson();

        return $response['comment']['id'];
    }

    /**
     * 测试动评论列表 用户登录状态.
     *
     * @return mixed
     */
    public function testGetFeedComments()
    {
        $user = $this->createUser();
        $feed = $this->addFeed($user);

        $this->addFeedComment($user, $feed);

        $response = $this
            ->actingAs($user, 'api')
            ->json('GET', "/api/v1/feeds/{$feed}/comments");
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['pinneds', 'comments']);
    }

    /**
     * 测试动态评论详情 用户未登录状态.
     *
     * @return mixed
     */
    public function testNotAuthGetFeedComments()
    {
        $user = $this->createUser();
        $feed = $this->addFeed($user);

        $this->addFeedComment($user, $feed);

        $response = $this
            ->json('GET', "/api/v1/feeds/{$feed}/comments");
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['pinneds', 'comments']);
    }

    /**
     * 测试动态评论详情 用户登录状态.
     *
     * @return mixed
     */
    public function testGetFeedCommentDetail()
    {
        $user = $this->createUser();
        $feed = $this->addFeed($user);
        $comment = $this->addFeedComment($user, $feed);

        $response = $this
            ->actingAs($user, 'api')
            ->json('GET', "/api/v1/feeds/{$feed}/comments/{$comment}");

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['body', 'user_id']);
    }

    /**
     * 测试动态评论详情 用户未登录状态.
     *
     * @return mixed
     */
    public function testNotAuthGetFeedCommentDetail()
    {
        $user = $this->createUser();
        $feed = $this->addFeed($user);
        $comment = $this->addFeedComment($user, $feed);

        $response = $this
            ->json('GET', "/api/v1/feeds/{$feed}/comments/{$comment}");

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['body', 'user_id']);
    }
}
