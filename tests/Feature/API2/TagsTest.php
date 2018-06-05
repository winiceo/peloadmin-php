<?php



namespace Leven\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\Tag as TagModel;
use Leven\Models\TagCategory as TagCategoryModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TagsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * The test set up.
     *
     * @author Seven Du <shiweidu@outlook.com>
     */
    protected function setUp()
    {
        parent::setUp();

        app(TagCategoryModel::class)->insert(['id' => '1', 'name' => '测试分类']);
        app(TagModel::class)->insert(['name' => '标签1', 'tag_category_id' => 1]);
        app(TagModel::class)->insert(['name' => '标签2', 'tag_category_id' => 1]);
    }

    /**
     * 测试获取标签.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testGetTags()
    {
        $response = $this->getJson('/api/v2/tags');

        $response->assertStatus(200);

        collect($response->json())->map(function ($array) {
            $this->assertArrayHasKey('id', $array);
            $this->assertArrayHasKey('name', $array);
            $this->assertArrayHasKey('tags', $array);
        });
    }
}
