<?php

declare(strict_types=1);



namespace Leven\Models\Relations;

use Leven\Models\NewWallet;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait UserHasNewWallet
{


    public function newWallets()
    {
        return $this->hasMany(NewWallet::class, 'user_id', 'id');
    }
}
