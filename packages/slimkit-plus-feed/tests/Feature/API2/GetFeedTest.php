<?php

declare(strict_types=1);



namespace SlimKit\PlusFeed\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;

class GetFeedTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected $feed;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(UserModel::class)->create();

        $this->feed = factory(Feed::class)->create([
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * 测试动态列表接口.
     *
     * @return mixed
     */
    public function testGetFeeds()
    {
        $response = $this->actingAs($this->user, 'api')
            ->json('GET', '/api/v1/feeds');
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['ad', 'pinned', 'feeds']);
    }

    /**
     * 测试动态详情接口.
     *
     * @return mixed
     */
    public function testGetFeed()
    {
        $response = $this->actingAs($this->user, 'api')
            ->json('GET', '/api/v1/feeds/'.$this->feed->id);
        $response
            ->assertStatus(200);
    }

    /**
     * 测试未登录获取动态列表接口.
     */
    public function testNotAuthGetFeeds()
    {
        $response = $this->json('GET', '/api/v1/feeds');
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['ad', 'pinned', 'feeds']);
    }

    /**
     * 测试动态详情接口.
     *
     * @return mixed
     */
    public function testNotAuthGetFeed()
    {
        $response = $this->json('GET', '/api/v1/feeds/'.$this->feed->id);
        $response
            ->assertStatus(200);
    }
}
