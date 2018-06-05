<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models;

use Leven\Models\User;
use Illuminate\Database\Eloquent\Model;
use Leven\Models\Comment as CommentModel;

class NewsPinned extends Model
{
    protected $table = 'news_pinneds';

    public function news()
    {
        if ($this->channel === 'news:comment') {
            return $this->hasOne(News::class, 'id', 'target');
        }

        return $this->hasOne(News::class, 'id', 'target');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function comment()
    {
        return $this->hasOne(CommentModel::class, 'id', 'raw');
    }
}
