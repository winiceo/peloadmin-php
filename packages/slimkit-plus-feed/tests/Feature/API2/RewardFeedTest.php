<?php

declare(strict_types=1);



namespace SlimKit\PlusFeed\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;

class RewardFeedTest extends TestCase
{
    use DatabaseTransactions;

    protected $owner;

    protected $other;

    protected $feed;

    public function setUp()
    {
        parent::setUp();

        $this->owner = factory(UserModel::class)->create();

        $this->other = factory(UserModel::class)->create();

        $this->feed = factory(Feed::class)->create([
            'user_id' => $this->owner->id,
        ]);
    }

    /**
     * 测试旧版打赏接口.
     *
     * @return mixed
     */
    public function testRewardFeed()
    {
        $this->other->wallet()->increment('balance', 1000);

        $response = $this
            ->actingAs($this->other, 'api')
            ->json('POST', "/api/v1/feeds/{$this->feed->id}/rewards", ['amount' => 10]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure(['message']);
    }

    /**
     * 获取动态打赏列表.
     *
     * @return mixed
     */
    public function testRewardFeedList()
    {
        $response = $this
            ->actingAs($this->other, 'api')
            ->json('get', "/api/v1/feeds/{$this->feed->id}/rewards");
        $response
            ->assertStatus(200);
    }
}
