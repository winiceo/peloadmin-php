<?php

declare(strict_types=1);



namespace Leven\Models\Relations;

use Leven\Models\RoleUser;

trait UserHasRole
{
    /**
     * Has roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function administrator()
    {
        return $this->HasMany(RoleUser::class, 'user_id', 'id')
            ->where('role_id', 1);
    }
}
