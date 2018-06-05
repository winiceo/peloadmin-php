<?php

declare(strict_types=1);



namespace Leven\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{

    protected $fillable = ['user_id', 'mobile', 'amount', 'status','time'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bill';


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
