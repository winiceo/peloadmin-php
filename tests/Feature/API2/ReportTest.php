<?php

declare(strict_types=1);



namespace Leven\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Leven\Models\Comment as CommentModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * The test user.
     *
     * @var Leven\Models\User
     */
    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(UserModel::class)->create();
        $this->target_user = factory(UserModel::class)->create();
    }

    /**
     * 测试举报用户.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testReportUser()
    {
        $response = $this->actingAs($this->user, 'api')->json('POST', 'api/v1/report/users/'.$this->target_user->id);

        $response->assertStatus(201);
    }

    /**
     * 测试举报评论.
     *
     * @return void
     * @author BS <414606094@qq.com>
     */
    public function testReportComment()
    {
        $comment = factory(CommentModel::class)->create([
            'user_id' => $this->target_user->id,
            'target_user' => $this->user->id,
            'reply_user' => 0,
            'body' => '测试',
            'commentable_id' => 1,
            'commentable_type' => 'system',
        ]);

        $response = $this->actingAs($this->user, 'api')->json('POST', 'api/v1/report/comments/'.$comment->id);

        $response->assertStatus(201);
    }

    protected function tearDown()
    {
        $this->user->forceDelete();
        $this->target_user->forceDelete();

        parent::tearDown();
    }
}
