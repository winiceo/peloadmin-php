<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\News as NewsModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\NewsCate as NewsCateModel;

class RewardNewsTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected $cate;

    protected $news;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(UserModel::class)->create();
        $this->cate = factory(NewsCateModel::class)->create();
        $this->news = factory(NewsModel::class)->create([
            'title' => 'test',
            'user_id' => $this->user->id,
            'cate_id' => $this->cate->id,
            'audit_status' => 1,
        ]);
    }

    /**
     * 旧版打赏接口.
     *
     * @return mixed
     */
    public function testRewardNews()
    {
        $this->user->wallet()->increment('balance', 100);
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('POST', "/api/v1/news/{$this->news->id}/rewards", [
                'amount' => 100,
            ]);
        $response
            ->assertStatus(201)
            ->assertJsonStructure(['message']);
    }

    /**
     * 新版打赏接口.
     *
     * @return mixed
     */
    public function testNewRewardNews()
    {
        $other = factory(UserModel::class)->create();

        $other->newWallet()->create([
            'balance' => 1000,
            'total_income' => 0,
            'total_expenses' => 0,
        ]);

        $response = $this
            ->actingAs($other, 'api')
            ->json('POST', "/api/v1/news/{$this->news->id}/new-rewards", [
                'amount' => 100,
            ]);
        $response
            ->assertStatus(201)
            ->assertJsonStructure(['message']);
    }

    /**
     * 资讯打赏列表.
     *
     * @return mixed
     */
    public function testGetNewsRewards()
    {
        $response = $this
            ->json('GET', "/api/v1/news/{$this->news->id}/rewards");
        $response
            ->assertStatus(200);
    }

    /**
     * 资讯打赏统计.
     *
     * @return mixed
     */
    public function testNewsRewardCount()
    {
        $response = $this
            ->json('GET', "/api/v1/news/{$this->news->id}/rewards/sum");
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['count', 'amount']);
    }
}
