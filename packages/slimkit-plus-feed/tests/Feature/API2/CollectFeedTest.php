<?php

declare(strict_types=1);



namespace SlimKit\PlusFeed\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;

class CollectFeedTest extends TestCase
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
     * 收藏动态.
     *
     * @return mixed
     */
    public function testLikeCollect()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('POST', "/api/v2/feeds/{$this->feed->id}/collections");

        $response
            ->assertStatus(201)
            ->assertJsonStructure(['message']);
    }

    /**
     * 收藏列表.
     *
     * @return mixed
     */
    public function testGetFeedCollections()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/v2/feeds/collections');

        $response
            ->assertStatus(200);
    }

    /**
     * 取消收藏.
     *
     * @return mixed
     */
    public function testUnCollectFeed()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('DELETE', "/api/v2/feeds/{$this->feed->id}/uncollect");

        $response
            ->assertStatus(204);
    }
}
