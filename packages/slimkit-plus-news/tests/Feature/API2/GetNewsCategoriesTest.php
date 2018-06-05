<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GetNewsCategoriesTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 已登录用户获取资讯栏目.
     *
     * @return mixed
     */
    public function testLoggedGetNewsCategories()
    {
        $user = factory(UserModel::class)->create();

        $response = $this
            ->actingAs($user, 'api')
            ->json('GET', '/api/v2/news/cates');
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['my_cates', 'more_cates']);
    }

    /**
     * 已登录用户获取资讯栏目.
     *
     * @return mixed
     */
    public function testNotLoggedGetNewsCategories()
    {
        $response = $this
            ->json('GET', '/api/v2/news/cates');
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['my_cates', 'more_cates']);
    }
}
