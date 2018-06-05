<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\Tag as TagModel;
use Leven\Models\User as UserModel;
use Leven\Models\TagCategory as TagCateModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\News as NewsModel;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\NewsCate as NewsCateModel;

class RevokedPublishNewsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 删除审核和通过的投稿.
     *
     * @return mixed
     */
    public function testRevokedNews()
    {
        $user = factory(UserModel::class)->create();
        $cate = factory(NewsCateModel::class)->create();

        $news = factory(NewsModel::class)->create([
            'user_id' => $user->id,
            'cate_id' => $cate->id,
            'audit_status' => 1,
            'contribute_amount' => 100,
        ]);

        $response = $this
            ->actingAs($user, 'api')
            ->json('PUT', "/api/v1/news/categories/{$cate->id}/news/{$news->id}");
        $response
            ->assertStatus(201)
            ->assertJsonStructure(['message']);
    }

    /**
     * 创建所需标签.
     *
     * @return mixed
     */
    protected function createTags()
    {
        $cate = factory(TagCateModel::class)->create();
        $tags = factory(TagModel::class, 3)->create([
            'tag_category_id' => $cate->id,
        ]);

        return $tags->pluck('id')->implode(',');
    }
}
