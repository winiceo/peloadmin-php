<?php

declare(strict_types=1);



namespace Leven\Models\Relations;

use Leven\Models\Comment;

trait UserHasComment
{
    /**
     * Has comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'id');
    }
}
