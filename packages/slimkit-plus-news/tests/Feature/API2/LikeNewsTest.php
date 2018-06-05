<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\News as NewsModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\NewsCate as NewsCateModel;

class LikeNewsTest extends TestCase
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
            'audit_status' => 0,
        ]);
    }

    /**
     * 资讯点赞.
     *
     * @return mixed
     */
    public function testLikeNews()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('POST', "/api/v1/news/{$this->news->id}/likes");
        $response
            ->assertStatus(201);
    }

    /**
     * 取消资讯点赞.
     *
     * @return mixed
     */
    public function testUnLikeNews()
    {
        $this->news->like($this->user->id);

        $response = $this
            ->actingAs($this->user, 'api')
            ->json('DELETE', "/api/v1/news/{$this->news->id}/likes");
        $response
            ->assertStatus(204);
    }
}
