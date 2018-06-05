<?php

declare(strict_types=1);



namespace Leven\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Illuminate\Database\Eloquent\Builder;

class WalletCash extends Model
{
    use SoftDeletes;

    /**
     * User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
