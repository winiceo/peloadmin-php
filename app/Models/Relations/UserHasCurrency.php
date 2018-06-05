<?php

declare(strict_types=1);



namespace Leven\Models\Relations;

use Leven\Models\Currency;

trait UserHasCurrency
{
    /**
     * user has currencies.
     *
     * @author BS <414606094@qq.com>
     */
    public function currency()
    {
        return $this->hasOne(Currency::class, 'owner_id', 'id');
    }
}
