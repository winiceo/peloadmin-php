<?php

declare(strict_types=1);



namespace SlimKit\PlusFeed\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Leven\Models\Comment as CommentModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\FeedPinned;

class AuditPinnedFeedCommentTest extends TestCase
{
    use DatabaseTransactions;

    protected $owner;

    protected $other;

    protected $feed;

    protected $comment;

    public function setUp()
    {
        parent::setUp();

        $this->owner = factory(UserModel::class)->create();

        $this->other = factory(UserModel::class)->create();

        $this->feed = factory(Feed::class)->create([
            'user_id' => $this->owner->id,
        ]);

        $this->comment = factory(CommentModel::class)->create([
            'user_id' => $this->other->id,
            'target_user' => $this->other->id,
            'body' => 'test',
            'commentable_id' => $this->feed->id,
            'commentable_type' => 'feeds',
        ]);

        $this->pinned = factory(FeedPinned::class)->create([
            'channel' => 'comment',
            'raw' => $this->feed->id,
            'target' => $this->comment->id,
            'user_id' => $this->other->id,
            'target_user' => $this->owner->id,
            'amount' => 10,
            'day' => 1,
        ]);
    }

    /**
     * 测试通过动态评论置顶.
     *
     * @return mixed
     */
    public function testPassPinnedFeedComment()
    {
        $response = $this
           ->actingAs($this->owner, 'api')
           ->json(
               'PATCH',
               "/api/v1/feeds/{$this->feed->id}/comments/{$this->comment->id}/currency-pinneds/{$this->pinned->id}");
        $response
           ->assertStatus(201)
           ->assertJsonStructure(['message']);
    }

    /**
     * 测试拒绝动态评论置顶.
     *
     * @return mixed
     */
    public function testRejectPinnedFeedComment()
    {
        $response = $this
            ->actingAs($this->owner, 'api')
            ->json(
                'DELETE',
                "/api/v1/user/feed-comment-currency-pinneds/{$this->pinned->id}");
        $response
            ->assertStatus(204);
    }
}
