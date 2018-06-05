<?php

declare(strict_types=1);



namespace SlimKit\PlusFeed\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;

class LikeFeedTest extends TestCase
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
     * 给动态点赞.
     *
     * @return mixed
     */
    public function testLikeFeed()
    {
        $response = $this

            ->actingAs($this->user, 'api')
            ->json('POST', "/api/v2/feeds/{$this->feed->id}/like");
        $response
            ->assertStatus(201)
            ->assertJsonStructure(['message']);
    }

    /**
     * 喜欢的人列表.
     *
     * @return mixed
     */
    public function testGetFeedLikePerson()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', "/api/v2/feeds/{$this->feed->id}/likes");
        $response
            ->assertStatus(200);
    }

    /**
     * 取消点赞.
     *
     * @return mixed
     */
    public function testUnLikeFeed()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('DELETE', "/api/v2/feeds/{$this->feed->id}/unlike");
        $response
            ->assertStatus(204);
    }
}
