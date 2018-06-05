<?php

declare(strict_types=1);



namespace Leven\Models\Relations;

use Leven\Models\BalanceWithdraw;

trait UserHasBalanceWithdraw
{
    /**
     * Wallet cshs.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function BalanceWithdraw()
    {
        return $this->hasMany(BalanceWithdraw::class, 'user_id', 'id');
    }
}
