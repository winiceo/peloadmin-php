<?php

declare(strict_types=1);



namespace Zhiyi\Component\ZhiyiPlus\PlusComponentNews\Models;

use Leven\Models\Tag;
use Leven\Models\User;
use Leven\Models\Report;
use Leven\Models\Comment;
use Leven\Models\FileWith;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes,
        Relations\NewsHasLike,
        Relations\NewsHasReward,
        Relations\NewsHasCollection;

    protected $table = 'news';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['storage', 'deleted_at', 'cate_id'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['category', 'image', 'pinned'];

    /**
     *  Has catrgory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function category()
    {
        return $this->hasOne(NewsCate::class, 'id', 'cate_id');
    }

    /**
     * Has image.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function image()
    {
        return $this->hasOne(FileWith::class, 'id', 'storage')
            ->select('id', 'size');
    }

    public function newsCount()
    {
        // 作者文章数
        return $this->hasMany(self::class, 'user_id', 'user_id');
    }

    public function user()
    {
        // 文章作者用户data信息
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     *  文章对应的评论.
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * 筛选已审核文章.
     */
    public function scopeByAudit(Builder $query): Builder
    {
        return $query->where('audit_status', 0);
    }

    /**
     *  资讯拥有的置顶评论.
     *  @author Wayne < qiaobinloverabbi@gmail.com >
     *  @return [type]
     */
    public function pinnedComments()
    {
        return $this->belongsToMany(Comment::class, 'news_pinneds', 'target', 'raw')
            ->where('channel', 'news:comment');
    }

    /**
     *  是否置顶.
     *  @author Wayne < qiaobinloverabbi@gmail.com >
     *  @return [type] [description]
     */
    public function pinned()
    {
        return $this->hasOne(NewsPinned::class, 'target', 'id')
            ->where('channel', 'news');
    }

    /**
     * Has tags of the news.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'taggables')
            ->withTimestamps();
    }

    /**
     * 资讯关联的删除申请记录.
     *
     * @return mixed
     * @author BS <414606094@qq.com>
     */
    public function applylog()
    {
        return $this->hasOne(NewsApplyLog::class, 'id', 'news_id');
    }

    /**
     * 被举报记录.
     *
     * @return morphMany
     * @author BS <414606094@qq.com>
     */
    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
