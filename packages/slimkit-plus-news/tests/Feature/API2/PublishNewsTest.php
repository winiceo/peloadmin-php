<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\Tag as TagModel;
use Leven\Models\User as UserModel;
use Leven\Models\TagCategory as TagCateModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\NewsCate as NewsCateModel;

class PublishNewsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 发布一个资讯.
     *
     * @return mixed
     */
    public function testPublishNews()
    {
        $user = factory(UserModel::class)->create();
        $cate = factory(NewsCateModel::class)->create();

        $response = $this
            ->actingAs($user, 'api')
            ->json('POST', "/api/v2/news/categories/{$cate->id}/news", [
                'title' => 'test',
                'subject' => 'test',
                'content' => 'test',
                'tags' => $this->createTags(),
                'from' => 'test',
                'image' => null,
                'author' => 'test',
                'text_content' => 'test',
            ]);
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
