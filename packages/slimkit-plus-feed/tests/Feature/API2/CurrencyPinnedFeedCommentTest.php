<?php

declare(strict_types=1);



namespace SlimKit\PlusFeed\Tests\Feature\API2;

use Leven\Tests\TestCase;
use Leven\Models\User as UserModel;
use Leven\Models\Comment as CommentModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Feed;

class CurrencyPinnedFeedCommentTest extends TestCase
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
    }

    /**
     * 积分申请动态评论置顶.
     *
     * @return mixed
     */
    public function testPinnedFeedComment()
    {
        $this->other->currency()->firstOrCreate([
            'sum' => 1000,
            'type' => 1,
        ]);

        $response = $this
            ->actingAs($this->other, 'api')
            ->json('POST', "/api/v2/feeds/{$this->feed->id}/comments/{$this->comment->id}/currency-pinneds", [
                'amount' => 100,
                'day' => 1,
            ]);
        $response
            ->assertStatus(201)
            ->assertJsonStructure(['message']);
    }
}
