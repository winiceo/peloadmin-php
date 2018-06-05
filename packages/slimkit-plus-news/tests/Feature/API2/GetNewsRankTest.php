<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Feature\API2;

use Leven\Tests\TestCase;

class GetNewsRankTest extends TestCase
{
    /**
     * 资讯排行榜.
     *
     * @return mixed
     */
    public function testCollectNews()
    {
        $response = $this
            ->json('get', '/api/v2/news/ranks');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([]);
    }
}
