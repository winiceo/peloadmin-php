<?php

declare(strict_types=1);



namespace Leven\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\Advertising as AdvertisingModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdvertisingTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 测试获取广告位.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testGetAdvertisingSpace()
    {
        $response = $this->json('GET', '/api/v1/advertisingspace');

        collect($response->json())->map(function ($adspace) {
            $this->assertArrayHasKey('id', $adspace);
            $this->assertArrayHasKey('channel', $adspace);
            $this->assertArrayHasKey('space', $adspace);
            $this->assertArrayHasKey('alias', $adspace);
            $this->assertArrayHasKey('allow_type', $adspace);
            $this->assertArrayHasKey('format', $adspace);
        });
    }

    /**
     * 测试获取一个广告位的广告列表.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testGetAdvertising()
    {
        $spaces = $this->json('GET', '/api/v1/advertisingspace')->json();

        collect($spaces)->map(function ($space) {
            factory(AdvertisingModel::class)->create(['space_id' => $space['id']]);
            factory(AdvertisingModel::class)->create(['space_id' => $space['id']]);
        });

        $response = $this->json('GET', '/api/v1/advertisingspace/'.$spaces[0]['id'].'/advertising');

        collect($response->json())->map(function ($ad) {
            $this->assertAdvertisingData($ad);
        });
    }

    /**
     * 测试批量获取广告.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testGetMuliteAdvertising()
    {
        $spaces = $this->json('GET', '/api/v1/advertisingspace')->json();

        collect($spaces)->map(function ($space) {
            factory(AdvertisingModel::class)->create(['space_id' => $space['id']]);
            factory(AdvertisingModel::class)->create(['space_id' => $space['id']]);
        });

        $response = $this->json('GET', '/api/v1/advertisingspace/advertising?space='.$spaces[0]['id']);

        collect($response->json())->map(function ($ad) {
            $this->assertAdvertisingData($ad);
        });
    }

    /**
     * 断言广告基本结构.
     */
    protected function assertAdvertisingData(array $singleData)
    {
        $this->assertArrayHasKey('id', $singleData);
        $this->assertArrayHasKey('space_id', $singleData);
        $this->assertArrayHasKey('title', $singleData);
        $this->assertArrayHasKey('type', $singleData);
        $this->assertArrayHasKey('data', $singleData);
        $this->assertArrayHasKey('sort', $singleData);
    }
}
