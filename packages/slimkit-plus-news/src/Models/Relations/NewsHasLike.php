<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models\Relations;

use Leven\Models\Like;
use Leven\Models\User;
use Illuminate\Support\Facades\Cache;

trait NewsHasLike
{
    /**
     * Has likes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    /**
     * Check user like.
     *
     * @param mixed $user
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function liked($user): bool
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        $cacheKey = sprintf('news-like:%s,%s', $this->id, $user);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $status = $this->likes()
            ->where('user_id', $user)
            ->first() !== null;

        Cache::forever($cacheKey, $status);

        return $status;
    }

    /**
     * Like news.
     *
     * @param mixed $user
     * @return mixed
     */
    public function like($user)
    {
        if ($user instanceof User) {
            $user = $user->id;
        }
        $this->forgetLike($user);

        $this->increment('digg_count', 1);

        return $this->getConnection()->transaction(function () use ($user) {
            return $this->likes()->firstOrCreate([
                'user_id' => $user,
                'target_user' => $this->user_id,
            ]);
        });
    }

    /**
     * Unlike feed.
     *
     * @param mixed $user
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function unlike($user)
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        $like = $this->likes()->where('user_id', $user)->first();
        $this->decrement('digg_count', 1);
        $this->forgetLike($user);

        return $like && $this->getConnection()->transaction(function () use ($like) {
            $like->delete();

            return true;
        });
    }

    /**
     * Forget like cache.
     *
     * @param mixed $user
     * @return void
     */
    public function forgetLike($user)
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        $cacheKey = sprintf('news-like:%s,%s', $this->id, $user);
        Cache::forget($cacheKey);
    }
}
