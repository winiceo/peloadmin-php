<?php

declare(strict_types=1);



namespace Leven\Models;

use Illuminate\Database\Eloquent\Model;

class UserRecommended extends Model
{
    protected $table = 'users_recommended';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
