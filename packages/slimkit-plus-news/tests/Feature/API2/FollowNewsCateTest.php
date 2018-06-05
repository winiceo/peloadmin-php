<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\NewsCate as NewsCateModel;

class FollowNewsCateTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 测试订阅资讯.
     *
     * @return mixed
     */
    public function testFollowNewsCate()
    {
        $user = factory(UserModel::class)->create();
        $cates = factory(NewsCateModel::class, 3)->create();

        $response = $this
            ->actingAs($user, 'api')
            ->json('patch', '/api/v1/news/categories/follows', ['follows' => $cates->pluck('id')->implode(',')]);
        $response
            ->assertStatus(201)
            ->assertJsonStructure(['message']);
    }
}
