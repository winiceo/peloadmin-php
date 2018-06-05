<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentFeed\Models;

use Leven\Models\User;
use Illuminate\Database\Eloquent\Model;

class FeedCollection extends Model
{
    protected $table = 'feed_collections';
    protected $fillable = [
        'user_id',
        'feed_id',
    ];

    public function feed()
    {
        return $this->belongsTo(Feed::class, 'id', 'feed_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'user_id');
    }
}
