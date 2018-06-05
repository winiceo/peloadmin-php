<?php

declare(strict_types=1);



namespace Leven\Models\Relations;

use Leven\Models\Like;

trait UserHasLike
{
    /**
     * Has likes for user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id', 'id');
    }

    /**
     * Has be likeds for user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function belikeds()
    {
        return $this->hasMany(Like::class, 'target_user', 'id');
    }
}
