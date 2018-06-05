<?php

declare(strict_types=1);



namespace Leven\Models\Relations;

use Leven\Models\BalanceCharge;

trait UserHasBalanceCharge
{
    /**
     * User wallet charges.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function walletCharges()
    {
        return $this->hasMany(BalanceCharge::class, 'user_id', 'id');
    }
}
