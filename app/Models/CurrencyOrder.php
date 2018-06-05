<?php

declare(strict_types=1);



namespace Leven\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyOrder extends Model
{
    /**
     * the owner of order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author BS <414606094@qq.com>
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }
}
