<?php

declare(strict_types=1);



namespace Leven\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NewWallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'total_expenses', 'total_income'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wallet';

    /**
     * The wallet owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
