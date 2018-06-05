<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\News as NewsModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\NewsCate as NewsCateModel;

class GetNewsListTest extends TestCase
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
        $this->news = factory(NewsModel::class, 10)->create([
            'title' => 'test',
            'user_id' => $this->user->id,
            'cate_id' => $this->cate->id,
            'audit_status' => 0,
        ]);
    }

    /**
     * 获取资讯列表.
     *
     * @return mixed
     */
    public function testGetNewsList()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/v2/news');
        $response
            ->assertStatus(200);
    }

    /**
     * 测试搜索资讯.
     *
     * @return mixed
     */
    public function testSearchNews()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/v2/news?key=test');
        $response
            ->assertStatus(200)
            ->assertJsonCount(10);
    }

    /**
     * 根据分类筛选资讯.
     *
     * @return mixed
     */
    public function testGetNewsByCate()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/v2/news?cate_id='.$this->cate->id);
        $response
            ->assertStatus(200)
            ->assertJsonCount(10);
    }

    /**
     * 推荐筛选资讯.
     *
     * @return mixed
     */
    public function testGetNewsByRecommend()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', '/api/v2/news?recommend=1');
        $response
            ->assertStatus(200)
            ->assertJsonCount(0);
    }

    /**
     * 获取单条资讯.
     *
     * @return mixed
     */
    public function testGetSingleNews()
    {
        $id = $this->news->pluck('id')->random();

        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', "/api/v2/news/{$id}/correlations");
        $response
            ->assertStatus(200);
    }
}
