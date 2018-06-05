<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models;

use Leven\Models\FileWith;
use Illuminate\Database\Eloquent\Model;

class NewsRecommend extends Model
{
    protected $table = 'news_recommend';

    public function cover()
    {
        return $this->hasOne(FileWith::class, 'id', 'cover')->select('id', 'size');
    }
}
