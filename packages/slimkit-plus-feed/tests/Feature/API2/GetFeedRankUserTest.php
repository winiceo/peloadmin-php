<?php

declare(strict_types=1);



namespace SlimKit\PlusFeed\Tests\Feature\API2;

use Leven\Tests\TestCase;

class GetFeedRankUserTest extends TestCase
{
    /**
     * 获取动态排行.
     *
     * @return mixed
     */
    public function testGetFeedRankUser()
    {
        $response = $this
            ->json('GET', '/api/v2/feeds/ranks');
        $response
            ->assertStatus(200)
            ->assertJsonStructure([]);
    }
}
