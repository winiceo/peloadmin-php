<?php

declare(strict_types=1);



namespace Leven\Models;

use Illuminate\Database\Eloquent\Model;

class Taggable extends Model
{
    protected $table = 'taggables';

    public function user()
    {
        return $this->belongsTo(User::class, 'taggable_id', 'id');
    }
}
