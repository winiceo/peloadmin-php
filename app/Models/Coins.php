<?php

declare(strict_types=1);



namespace Leven\Models;

use Illuminate\Database\Eloquent\Model;

class Coins extends Model
{

    protected $fillable = ['name', 'symbol', 'decimals', 'withdraw_enable','fee'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'coins';


}
