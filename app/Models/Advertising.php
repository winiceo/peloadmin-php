<?php

declare(strict_types=1);



namespace Leven\Models;

use Illuminate\Database\Eloquent\Model;

class Advertising extends Model
{
    protected $table = 'advertising';

    protected $fillable = ['space_id', 'type', 'title', 'data', 'sort'];

    protected $casts = [
        'data' => 'array',
    ];

    public function space()
    {
        return $this->belongsTo(AdvertisingSpace::class);
    }
}
