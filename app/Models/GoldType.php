<?php

declare(strict_types=1);



namespace Leven\Models;

use Illuminate\Database\Eloquent\Model;

class GoldType extends Model
{
    public $table = 'gold_types';

    public $fillable = ['name', 'unit', 'status'];
}
