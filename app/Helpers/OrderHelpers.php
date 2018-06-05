<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\Paginator;

trait OrderHelpers
{

    public static function get()
    {
        return [
            "CREATED" => 0,
            "PAY" => 1,
            "RELEASE" => 3,
            "CANCEL" => 5,
            "FINISH" => 7,
            "COMPLAINT" => 11,
        ];


    }
}
