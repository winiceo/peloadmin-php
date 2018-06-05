<?php



namespace Leven\Tests\Feature\API2;

use Leven\Tests\TestCase;

class LocationsTest extends TestCase
{
    /**
     * 测试获取地区.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testSearchLocations()
    {
        $response = $this->get('/api/v1/locations/search?name=北京');

        $response->assertStatus(200);
    }

    /**
     * 测试获取热门城市.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testGetHotLocations()
    {
        $response = $this->get('/api/v1/locations/hots');

        $response->assertStatus(200);
    }
}
