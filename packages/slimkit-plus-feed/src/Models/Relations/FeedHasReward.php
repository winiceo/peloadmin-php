<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models\Relations;

use DB;
use Leven\Models\User;
use Leven\Models\Reward;
use Illuminate\Support\Facades\Cache;

trait FeedHasReward
{
    public function rewards()
    {
        return $this->morphMany(Reward::class, 'rewardable');
    }

    /**
     * Reward a author of feed.
     *
     * @author bs<414606094@qq.com>
     * @param  mix $user
     * @param  float $amount
     * @return mix
     */
    public function reward($user, $amount)
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        $cacheKey = sprintf('feed-reward-count:%s', $this->id);
        Cache::forget($cacheKey);

        return $this->getConnection()->transaction(function () use ($user, $amount) {
            return $this->rewards()->create([
                'user_id' => $user,
                'target_user' => $this->user_id,
                'amount' => $amount,
            ]);
        });
    }

    /**
     * 打赏总数统计
     *
     * @author bs<414606094@qq.com>
     * @return mix
     */
    public function rewardCount()
    {
        $cacheKey = sprintf('feed-reward-count:%s', $this->id);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $counts = $this->rewards()->select(DB::raw('count(*) as count, sum(amount) as amount'))->first()->toArray();

        Cache::forever($cacheKey, $counts);

        return $counts;
    }
}
