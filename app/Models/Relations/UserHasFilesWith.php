<?php

declare(strict_types=1);



namespace Leven\Models\Relations;

use Leven\Models\FileWith;

trait UserHasFilesWith
{
    /**
     * user files.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function files()
    {
        return $this->hasMany(FileWith::class, 'user_id', 'id');
    }
}
