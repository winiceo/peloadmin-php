<?php

namespace Leven\Helpers;

use Leven\Models\Coins;
use Illuminate\Contracts\Pagination\Paginator;

trait CoinHelpers
{

    public static function get()
    {
        $coins=Coins::get();
        $temp=[];
        foreach ($coins as $coin){
            $temp[]= $coin->toArray();
        }


        return $temp;
    }

    public static function getIds()
    {
        $coins=CoinHelpers::get();
        $temp=[];
        foreach ($coins as $coin){

             $temp[$coin['id']]=$coin;
        }
        return $temp;
    }


}
