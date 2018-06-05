<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Leven\Models\Comment as CommentModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\News as NewsModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\NewsCate as NewsCateModel;

class CommentNewsTest extends TestCase
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
     * 获取资讯列表.
     *
     * @return mixed
     */
    public function testCommentNews()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('POST', "/api/v1/news/{$this->news->id}/comments", [
                'body' => 'test',
                'reply_user' => 0,
            ]);
        $response
            ->assertStatus(201);
    }

    /**
     * 获取资讯下面的评论列表.
     *
     * @return mixed
     */
    public function testGetNewsComment()
    {
        $response = $this
            ->actingAs($this->user, 'api')
            ->json('GET', "/api/v1/news/{$this->news->id}/comments");
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['pinneds', 'comments']);
    }

    /**
     * 删除资讯下面的评论.
     *
     * @return mixed
     */
    public function testDeleteNewsComment()
    {
        $comment = factory(CommentModel::class)->create([
            'user_id' => $this->user->id,
            'target_user' => 0,
            'body' => 'test',
            'commentable_id' => $this->news->id,
            'commentable_type' => 'news',
        ]);

        $response = $this
            ->actingAs($this->user, 'api')
            ->json('DELETE', "/api/v1/news/{$this->news->id}/comments/{$comment->id}");
        $response
            ->assertStatus(204);
    }
}
