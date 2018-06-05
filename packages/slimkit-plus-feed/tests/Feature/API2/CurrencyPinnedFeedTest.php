<?php

declare(strict_types=1);



namespace SlimKit\PlusFeed\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;

class CurrencyPinnedFeedTest extends TestCase
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
     * 不传置顶天数和置顶金额.
     *
     * @return mixed
     */
    public function testNonParamsCurrencyPinnedFeed()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('POST', "/api/v2/feeds/{$this->feed->id}/currency-pinneds");
        $response
            ->assertStatus(422);
    }

    /**
     * 余额不足.
     *
     * @return mixed
     */
    public function testNotEnoughCurrencyPinnedFeed()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('POST', "/api/v2/feeds/{$this->feed->id}/currency-pinneds", [
                'amount' => 1000,
                'day' => 10,
            ]);
        $response
            ->assertStatus(422);
    }

    /**
     * 置顶动态.
     *
     * @return mixed
     */
    public function testCurrencyPinnedFeed()
    {
        $this->user->currency()->firstOrCreate([
            'sum' => 1000,
            'type' => 1,
        ]);

        $response = $this
            ->actingAs($this->user, 'api')
            ->json('POST', "/api/v2/feeds/{$this->feed->id}/currency-pinneds", [
                'amount' => 1000,
                'day' => 10,
            ]);
        $response
            ->assertStatus(201);
    }
}
